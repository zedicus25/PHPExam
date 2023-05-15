@extends('layouts.app')

@section('repos')

    <div class="list-group">
        @foreach($repos as $repo)
                <h4 class="list-group-item-heading">{{ $repo->name }}</h4>
                <p class="list-group-item-text">{{ $repo->description }}</p>
        @endforeach
    </div>

@endsection
