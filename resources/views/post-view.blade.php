@extends('layouts.app')

@section('content')

    <div class="main-container post-view">
        <div id="post-view">
            @include('modules.post')
        </div>

        @include('modules.post-view-navigation')
        <script src="{{ asset('js/control/post-navigation.js') }}"></script>
        <script src="{{ asset('js/control/post.js') }}"></script>

        @include('layouts.modals')

    </div>

@endsection
