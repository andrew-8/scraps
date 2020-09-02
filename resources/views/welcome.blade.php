@extends('layouts.app')

@section('content')
        <div class="flex-center container full-height">
            <div class="content">
                <div class="d-flex mb-3 justify-content-between align-items-center">
                    <h2 class="mr-auto">
                        Notes
                    </h2>
                    @auth
                        {!! link_to('admin/scraps', 'My notes', $attributes = array('class' => 'mr-3'), $secure = null); !!}
                        {!! link_to_route('scraps.create', 'Create note', null,['class' => 'btn btn-outline-primary']) !!}
                    @endauth
                </div>
                @if($scraps)
                <ul class="list-group">
                    @foreach($scraps as $key => $scrap)
                        @auth
                            @php $user = (Auth::user()->id == $scrap->user_id)? true: false @endphp
                            <li class="list-group-item {!! ($user)? 'd-flex justify-content-between align-items-center': '' !!}"><a href="{{ route('scrap',$scrap->id) }}">{{$scrap->title}}</a>
                                @if ($user)
                                    <a class="edit-scrap" href="{{ route('scraps.edit', ['scrap' => $scrap->id]) }}">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </a>
                                @endif
                            </li>
                        @else
                            <li class="list-group-item"><a href="{{ route('scrap',$scrap->id) }}">{{$scrap->title}}</a></li>
                        @endauth
                    @endforeach
                </ul>
                @else
                    <p>List is empty</p>
                @endif
                @if(isset($privateScraps) && $privateScraps)
                    <div class="mt-5">
                        <h2>Notes who shared with you</h2>
                        <ul class="list-group">
                            @foreach($privateScraps as $key => $scrap)
                                @auth
                                    <li class="list-group-item d-flex justify-content-between align-items-center"><a href="{{ route('scrap',$scrap->id) }}">{{$scrap['title']}}</a>
                                        <span class="author-scrap">
                                            author: {{$scrap['user']['name']}} ({{ $scrap['user']['email'] }})
                                        </span>
                                    </li>
                                @endauth
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
@endsection
