@extends('layouts.app')
@section('title', __('messages.settings'))
@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('messages.settings')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Crm\Http\Controllers\CrmSettingsController::class, 'updateSettings']), 'method' => 'post']))->open() !!}
            @component('components.widget', ['class' => 'box-solid'])
                <div class="col-md-4">
                    <div class="checkbox">
                        <label>
                        {!! html()->checkbox('enable_order_request', !empty($crm_settings['enable_order_request']), 1)->attributes(['class' => 'input-icheck']); !!} @lang('crm::lang.enable_order_request')
                        </label> @show_tooltip(__('crm::lang.enable_order_request_help'))
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('crm::lang.order_request_prefix') . ':', 'order_request_prefix')->attributes([]) !!}
                        {!! html()->text('order_request_prefix', $crm_settings['order_request_prefix'] ?? null)->attributes(['class' => 'form-control','placeholder' => __( 'crm::lang.order_request_prefix' )]); !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.update' )</button>
                </div>
            @endcomponent
            {!! html()->form()->close() !!}
        </div>
    </div>
</section>
@stop
@section('javascript')
@endsection