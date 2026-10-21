@extends('layouts.app')
@section('title', __('woocommerce::lang.api_settings'))

@section('content')
@include('woocommerce::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.api_settings')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@updateSettings', 'method' => 'post']))->open() !!}
    <div class="row">
        <div class="col-xs-12">
           <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('woocommerce::lang.instructions')</a>
                        <a href="#" class="list-group-item text-center">@lang('woocommerce::lang.api_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('woocommerce::lang.product_sync_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('woocommerce::lang.order_sync_settings')</a>
                        <a href="#" class="list-group-item text-center">@lang('woocommerce::lang.webhook_settings')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('woocommerce::woocommerce.partials.api_instructions')
                    @include('woocommerce::woocommerce.partials.api_settings')
                    @include('woocommerce::woocommerce.partials.product_sync_settings')
                    @include('woocommerce::woocommerce.partials.order_sync_settings')
                    @include('woocommerce::woocommerce.partials.webhook_settings')
                </div>
            </div>

            <div class="col-xs-12">
                <p class="help-block"><i>{!! __('woocommerce::lang.version_info', ['version' => $module_version]) !!}</i></p>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group pull-right">
            {{html()->input('submit', null, 'update')->attributes(['class'=>"btn btn-danger"])}}
            </div>
        </div>
    </div>
    {!! html()->form()->close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        $('.create_quantity').on('ifChecked', function(event){
            $('.create_stock_settings').each( function(){
                $(this).addClass('hide');
            });
        });
        $('.create_quantity').on('ifUnchecked', function(event){
            $('.create_stock_settings').each( function(){
                $(this).removeClass('hide');
            });
        });
        $('.update_quantity').on('ifChecked', function(event){
            $('.update_stock_settings').each( function(){
                $(this).addClass('hide');
            });
        });
        $('.update_quantity').on('ifUnchecked', function(event){
            $('.update_stock_settings').each( function(){
                $(this).removeClass('hide');
            });
        });
    });
</script>
@endsection