@if(Session::has('status'))
    <div class="alert {{Session::get('class')}}">
        {{ Session::get('status')}}
    </div>
@endif
@if (Session::has('error'))
    <div class="alert alert-danger">
        @if (is_array(Session::get('error')))
            @foreach (Session::get('error') as $error)
                {{ $error }}<br>
            @endforeach
        @else
            {{Session::get('error')}}
        @endif
    </div>
@endif
@if ($scraps)
<div class="table-responsive my-4">
    <table class="table table-hover">
        <thead class="thead-light">
        <tr>
            <th class="align-left">ID</th>
            <th>Title</th>
            <th>Publish</th>
            <th>Private note</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($scraps as $scrap)
            <tr>
                <td class="align-left">{{$scrap->id}}</td>
                <td class="align-left">{!! link_to_route('scraps.edit', $scrap->title, ['scrap' => $scrap->id]) !!}</td>
                <td class="align-left">
                    @if ($scrap->publish > 0)
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle" class="svg-inline--fa fa-check-circle fa-w-16 fa-2x" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path class="fa-w-16 fa-2x" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path></svg>
                    @else
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times-circle" class="svg-inline--fa fa-times-circle fa-w-16 fa-2x" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path class="fa-w-16 fa-2x" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"></path></svg>
                    @endif
                </td>
                <td class="align-left">
                    @if ($scrap->private > 0)
                        Yes
                    @else
                        No
                    @endif
                </td>
                <td>
                    {!! Form::open(['url' => route('scraps.destroy', ['scrap' => $scrap->id]), 'class' => 'form-horizontal', 'method' => 'POST']) !!}
                    @method('DELETE')
                    @csrf
                    {!! Form::button('Delete', ['class' => 'btn btn-sm btn-danger', 'type'=>'submit']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@else
    <p>Your list is empty</p>
@endif
{!! link_to_route('scraps.create', 'Create note', null, ['class' => 'btn btn-primary']) !!}