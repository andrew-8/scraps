@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @if ($access)
                <div class="d-flex mb-3 justify-content-between align-items-center">
                    <h2 class="mr-auto">{{ $scrap->title }}</h2>
                    @auth
                        {!! link_to('admin/scraps', 'My notes', $attributes = array('class' => 'mr-3'), $secure = null); !!}
                        @if ($edit) {!! link_to_route('scraps.edit', 'Edit note', ['scrap' => $scrap->id],['class' => 'btn btn-primary mr-3']) !!} @endif
                        {!! link_to_route('scraps.create', 'Create note', null,['class' => 'btn btn-outline-primary']) !!}
                    @endauth
                </div>
                <div class="content">
                    {!! $scrap->text !!}
                </div>
                <div class="author text-right">
                    {{ 'by '.$scrap->user_id->name .' - created '. $scrap->created_at}}
                </div>
                @else
                    <p>Access to content denied</p>
                @endif
            </div>
        </div>
    </div>
@endsection
