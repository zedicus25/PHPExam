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
    public int $page = 1;
    public function index(Request $request)
    {
        $userName = $request->input('userName');

        if($userName == null){
            echo "Dont find!";
            return view('repos', ['repos' => array()]);
        }
        session(['page' => 1]);
        $repos = $this->getRepos($userName,1);

        return view('repos', ['repos' => $repos]);
    }



    public function getRepos(string $userName, $page) {
        $url = "https://api.github.com/users/$userName/repos?page={$page}";
        $key = env('GITHUB_TOKEN');
        return json_decode(Http::withHeaders([
            'Authorization' => "Bearer $key"
        ])->get($url));
    }

    public function goToNextPage(Request $request)
    {
        $userName = $request->input('userName');

        if($userName == null){
            echo "Dont find!";
            return view('repos', ['repos' => array()]);
        }

        $page = session('page');
        if($page == null){
            $page = 1;
            session(['page' => 1]);
        }
        else{
            $page = $page + 1;
            session(['page' => $page]);
        }

        $repos = $this->getRepos($userName, $page);

        return view('repos', ['repos' => $repos]);
    }

    public function goToPreviousPage(Request $request)
    {
        $userName = $request->input('userName');

        if($userName == null){
            echo "Dont find!";
            return view('repos', ['repos' => array()]);
        }

        $page = session('page');
        if($page == null){
            $page = 1;
            session(['page' => 1]);
        }
        else{
            if($page > 1){
                $page = $page - 1;
                session(['page' => $page]);
            }
        }

        $repos = $this->getRepos($userName,$page);

        return view('repos', ['repos' => $repos]);
    }

    public function openRepo(Request $request)
    {
        $repo = $request->query('repo');
        $path = $request->query('path');;
        $userName = $request->query('userName');;
        $url = "https://api.github.com/repos/$userName/$repo/contents/$path";
        $key = env('GITHUB_TOKEN');
        $files = json_decode(Http::withHeaders([
            'Authorization' => "Bearer $key"
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
            if(array_key_exists('name', $files))
            {
                $response = $client->get($files['download_url']);

                $fileName = $files['name'];
                $fileContent = $response->getBody()->getContents();

                $zip->addFromString($fileName, $fileContent);
            }
            else
            {
                foreach ($files as $item)
                {
                    if ($item['type'] == 'dir') {
                        $this->addFolderToZip($item, $zip, $client);
                    } else {
                        $response = $client->get($item['download_url']);

                        $fileName = $item['name'];
                        $fileContent = $response->getBody()->getContents();

                        $zip->addFromString($fileName, $fileContent);
                    }
                }
            }



            $zip->close();
            return response()->download($zipFile);
        }

        return redirect()->back()->with('error', 'Cannot download');
    }

    private function addFolderToZip($folder, $zip, $client)
    {
        $key = env('GITHUB_TOKEN');
        $folderContent = json_decode(Http::withHeaders([
            'Authorization' => "Bearer $key"
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
