@extends('layouts.app')

@php
    $page_title = __( 'essentials::lang.add_knowledge_base' );
    $kb_type = '';
    if(!empty($parent)) {
        $kb_type = $parent->kb_type == 'knowledge_base' ? 'section' : 'article';
    }
    if($kb_type == 'section') {
        $page_title = __( 'essentials::lang.add_section' );
    } else if($kb_type == 'article') {
        $page_title = __( 'essentials::lang.add_article' );
    }
@endphp
@section('title', $page_title)

@section('content')
@include('essentials::layouts.nav_essentials')

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\KnowledgeBaseController::class, 'store']), 'method' => 'post' ]))->open() !!}
    @if(!empty($parent))
        {!! html()->hidden('kb_type', $kb_type)->attributes([]); !!}
        {!! html()->hidden('parent_id', $parent->id)->attributes([]); !!}
    @endif
    @component('components.widget', ['title' => $page_title])
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'essentials::lang.title' ) . ':*', 'title')->attributes([]) !!}
                    {!! html()->text('title', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.title' ), 'required' ]); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label(__( 'essentials::lang.content' ) . ':', 'content')->attributes([]) !!}
                    {!! html()->textarea('content', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.content' ) ]); !!}
                </div>
            </div>
            @if(empty($parent))
                <div class="col-md-6">
                    <div class="form-group">
                        {!! html()->label(__( 'essentials::lang.share_with' ) . ':', 'share_with')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('share_with', ['public' => __('essentials::lang.public'), 'private' => __( 'essentials::lang.private' ), 'only_with' => __( 'essentials::lang.only_with' )], 'public', ['class' => 'form-control select2' ])); !!}
                    </div>
                </div>
                <div class="col-md-6" id="user_ids_div" style="display: none;">
                    <div class="form-group">
                        {!! html()->label(__( 'essentials::lang.share_only_with' ) . ':', 'user_ids')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('user_ids[]', $users, null, ['class' => 'form-control select2', 'multiple', 'id' => 'user_ids', 'style' => 'width: 100%;' ])); !!}
                    </div>
                </div>
            @endif
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.save' )</button>
            </div>
        </div>
    @endcomponent
{!! html()->form()->close() !!}
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        init_tinymce('content');

        $('#share_with').change( function() {
            if ($(this).val() == 'only_with') {
                $('#user_ids_div').fadeIn();
            } else {
                $('#user_ids_div').fadeOut();
            }
        });
    });
</script>
@endsection
