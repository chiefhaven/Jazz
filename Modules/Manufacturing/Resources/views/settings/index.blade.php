@extends('layouts.app')
@section('title', __('messages.settings'))

@section('content')
@include('manufacturing::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('messages.settings')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Manufacturing\Http\Controllers\SettingsController::class, 'store']), 'method' => 'post', 'id' => 'manufacturing_settings_form' ]))->open() !!}
    <div class="row">
        <div class="col-xs-12">
           <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('messages.settings')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    <div class="pos-tab-content active">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! html()->label(__('manufacturing::lang.mfg_ref_no_prefix') . ':', 'ref_no_prefix')->attributes([]) !!}
                                    {!! html()->text('ref_no_prefix', !empty($manufacturing_settings['ref_no_prefix']) ? $manufacturing_settings['ref_no_prefix'] : null)->attributes(['placeholder' => __('manufacturing::lang.mfg_ref_no_prefix'), 'class' => 'form-control']); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <br>
                                    <div class="checkbox">
                                        <label>
                                        {!! html()->checkbox('disable_editing_ingredient_qty', !empty($manufacturing_settings['disable_editing_ingredient_qty']), 1)->attributes(['class' => 'input-icheck', 'id' => 'disable_editing_ingredient_qty']); !!} @lang('manufacturing::lang.disable_editing_ingredient_qty')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <br>
                                    <div class="checkbox">
                                        <label>
                                        {!! html()->checkbox('enable_updating_product_price', !empty($manufacturing_settings['enable_updating_product_price']), 1)->attributes(['class' => 'input-icheck', 'id' => 'enable_updating_product_price']); !!} @lang('manufacturing::lang.enable_editing_product_price_after_production')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
        </div>
    </div>

    <div class="col-xs-12">
        <p class="help-block"><i>{!! __('manufacturing::lang.version_info', ['version' => $version]) !!}</i></p>
    </div>
    {!! html()->form()->close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function () {
        $(".file-input").fileinput(fileinput_setting);
    });
</script>

@endsection