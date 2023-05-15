<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class GithubController extends Controller
{
    public function index()
    {
        $repos = $this->getRepos();

        return view('repos', ['repos' => $repos]);
    }

    public function getRepos() {
        $url = "https://api.github.com/users/zedicus25/repos";

        return json_decode(Http ::get($url));
    }
}
