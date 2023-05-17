<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class GithubController extends Controller
{
    public function index(Request $request)
    {
        $userName = $request->input('userName');

        if($userName == null){
            echo "Dont find!";
            return array();
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
}
