@extends('layouts.app')

@section('repos')

    <form action="/repos" method="GET">
        @csrf
        <label for="userName">User Name:</label>
        <input required type="text" id="userName" name="userName">
        <button type="submit">Find</button>
    </form>

    @if(!isset($repos->message))
        <div class="list-group">
            @foreach($repos as $repo)
                <div class="list-group-item" >
                    <h4 class="list-group-item-heading">
                        <a href="{{ route('callControllerMethod', ['repo' => $repo->name, 'path' => ".", 'userName'=> $repo->owner->login]) }}">{{$repo->name}}</a>
                    </h4>
                    <p class="list-group-item-text">{{ $repo->description }}</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a class="btn btn-primary me-md-2" target="_blank" href="{{ $repo->html_url }}">GitHub</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div>{{$repos->message}}</div>
    @endif

@endsection
