<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;
use ZipArchive;
use function PHPUnit\Framework\exactly;

class GithubController extends Controller
{
    public function index(Request $request)
    {
        $userName = $request->input('userName');

        if($userName == null){
            echo "Dont find!";
            return view('repos', ['repos' => array()]);
        }

        $repos = $this->getRepos($userName);

        return view('repos', ['repos' => $repos]);
    }

    public function getRepos(string $userName) {
        $url = "https://api.github.com/users/$userName/repos";

        return json_decode(Http::withHeaders([
            'Authorization' => 'Bearer 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'
        ])->get($url));
    }

    public function openRepo(Request $request)
    {
        $repo = $request->query('repo');
        $path = $request->query('path');;
        $userName = $request->query('userName');;
        $url = "https://api.github.com/repos/$userName/$repo/contents/$path";
        $files = json_decode(Http::withHeaders([
            'Authorization' => 'Bearer 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'
        ])->get($url));
        return view('selectedRepo')->with('files', ['content' => $files, 'repoName'=> $repo, 'userName'=>$userName]);
    }

    public function downloadRepo(Request $request)
    {
        $files = $request->query('files');
        $repoName = $request->query('repoName');
        if($files == null || $repoName == null)
            return;

        $this->downloadZip($files, $repoName);
        return redirect()->back()->with('success', 'Repo was downloaded!');
    }



    private function downloadZip($files, $repoName)
    {
        $zipFile = public_path($repoName.".zip");
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) === true) {
            $client = new Client();

            foreach ($files as $item) {

                if ($item['type']== 'dir') {
                    $this->addFolderToZip($item, $zip, $client);
                } else {
                    $response = $client->get($item['download_url']);

                    $fileName = $item['name'];
                    $fileContent = $response->getBody()->getContents();

                    $zip->addFromString($fileName, $fileContent);
                }
            }

            $zip->close();
            return response()->download($zipFile);
        }

        return redirect()->back()->with('error', 'Cannot download');
    }

    private function addFolderToZip($folder, $zip, $client)
    {
        $folderContent = json_decode(Http::withHeaders([
            'Authorization' => 'Bearer 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'
        ])->get($folder['url']));

        $zip->addEmptyDir($folder['name']);

        foreach ($folderContent as $item) {

                if ($item->type == 'dir') {
                    $this->addFolderToZip($item, $zip, $client);
                } else {
                    $response = $client->get($item->download_url);

                    $fileName = $item->name;
                    $fileContent = $response->getBody()->getContents();
                    $zip->addFromString($fileName,$fileContent);
                }

        }
    }

}
