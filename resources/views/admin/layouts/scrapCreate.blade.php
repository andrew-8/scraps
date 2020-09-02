{!! Form::open(['url' => (isset($scrap->id)) ? route('scraps.update',['scrap' => $scrap->id]) : route('scraps.store'), 'class'=> 'contact-form my-4', 'method'=>'POST', 'enctype'=>'multipart/form-data']) !!}
@if(isset($scrap->id))
    @method('PUT')
@endif
@csrf
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
<div class="row">
    <div class="col">
        <div class="form-group">
            {!! Form::label('title', 'Title of note:'); !!}
            {!! Form::text('title', isset($scrap->title) ? $scrap->title : Request::old('title'), ['class'=>'form-control', 'id' => 'title']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="short-desc">Description:</label>
            {!! Form::textarea('text', isset($scrap->text) ? $scrap->text : Request::old('text'), ['id' => 'editor', 'class' => 'form-control', 'rows' => 4])!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <div class="custom-control mr-auto custom-checkbox">
                {{Form::hidden('publish', 0)}}
                {!! Form::checkbox('publish', isset($scrap->publish) ? $scrap->publish : 1, isset($scrap->publish) ? $scrap->publish : true , ['class' => 'custom-control-input check-input', 'id' => 'checkPublish'])!!}
                {!! Form::label('checkPublish', 'Publish', ['class' => 'custom-control-label']); !!}
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control mr-auto custom-checkbox">
                {{Form::hidden('private', 0)}}
                {!! Form::checkbox('private', isset($scrap->private) ? $scrap->private : 0, isset($scrap->private) ? $scrap->private : false , ['class' => 'custom-control-input check-input', 'id' => 'checkPrivate'])!!}
                {!! Form::label('checkPrivate', 'Private note', ['class' => 'custom-control-label']); !!}
            </div>
        </div>
    </div>
</div>
<div class="row emails-show {{ (isset($scrap) && $scrap->private) ? 'active': ''}}">
    <div class="col">
        <div class="form-group">
            {!! Form::label('emails', 'With whom to share'); !!}
            {!! Form::select('emails[]', isset($emails) ? $emails: array(), isset($emails) ? $emails: '', ['class' => 'tokenize-source-email', 'style' => 'display:none', 'multiple']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group text-left">
            {!! Form::button('Save note', ['class' => 'btn btn-primary', 'type'=>'submit']) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
    $(function(){
        $(document).on('change', 'input.check-input', function(){
            if ($(this).prop('checked')){
                $(this).val(1);
            } else {$(this).val(0);}
        });

        $(document).on('change', '#checkPrivate', function(){
            $('.emails-show').fadeToggle();
        });

        CKEDITOR.replace('editor', {
            allowedContent: true,
        });
    });

    $('.tokenize-source-email').tokenize2({
        dataSource: function(term, object){
            $.ajax('{{url("admin/search-emails")}}', {
                data: { search: term, start: 0 },
                dataType: 'json',
                success: function(data){
                    if (data.error) {
                        $('.alert-danger').text(data.error);
                    }
                    var $items = [];
                    $.each(data, function(k, v){
                        $items.push(v);
                    });
                    object.trigger('tokenize:dropdown:fill', [$items]);
                }
            });
        },
        tokensAllowCustom: true
    });

    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }

</script>