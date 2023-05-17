@extends('layouts.app')

@section('files')

        @if(is_array($files['content']))
            <ul class="list-group">
                @foreach($files['content'] as $cont)
                    <li class="list-group-item">
                        @if($cont->type == 'file')
                            <span class="badge">F</span>
                            <a href="{{ route('callControllerMethod', ['repo' => $files['repoName'], 'path' => $cont->path, 'userName'=> $files['userName']]) }}">{{ $cont->name }}</a>
                        @else
                            <span class="badge">D</span>
                            <a href="{{ route('callControllerMethod', ['repo' => $files['repoName'], 'path' => $cont->path, 'userName'=> $files['userName']]) }}">{{ $cont->name }}</a>
                       @endif
                    </li>
              @endforeach
            </ul>
        @else
            <textarea  style='resize: none;' readonly cols="55" rows="35" class="form-control">{{base64_decode($files['content']->content,true)}}</textarea>
        @endif
    <a class="btn btn-primary me-md-2" href="{{ redirect()->getUrlGenerator()->previous() }}">Back</a>
    <a class="btn btn-primary me-md-2" href="{{ route("downloadZip", ['files'=> $files['content'], 'repoName' => $files['repoName']]) }}">Download</a>
@endsection

