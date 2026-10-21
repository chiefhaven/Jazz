@extends('layouts.app')
@section('title',  __('printer.add_printer'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('printer.add_printer')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\PrinterController::class, 'store']), 'method' => 'post', 'id' => 'add_printer_form' ]))->open() !!}
	@component('components.widget')
    <div class="row">
      <div class="col-sm-12">
        <div class="form-group">
          {!! html()->label(__('printer.name') . ':*', 'name')->attributes([]) !!}
            {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required',
            'placeholder' => __('lang_v1.printer_name_help')]); !!}
        </div>
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          {!! html()->label(__('printer.connection_type') . ':*', 'connection_type')->attributes([]) !!}
          {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('connection_type', $connection_types, null, ['class' => 'form-control select2'])); !!}
        </div>
      </div>

      <div class="col-sm-12">
        <div class="form-group">
          {!! html()->label(__('printer.capability_profile') . ':*', 'capability_profile')->attributes([]) !!}
          @show_tooltip(__('tooltip.capability_profile'))
          {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('capability_profile', $capability_profiles, null, ['class' => 'form-control select2'])); !!}
        </div>
      </div>

      <div class="col-sm-12">
        <div class="form-group">
          {!! html()->label(__('printer.character_per_line') . ':*', 'char_per_line')->attributes([]) !!}
            {!! html()->input('number', 'char_per_line', 42)->attributes(['class' => 'form-control', 'required',
            'placeholder' => __('lang_v1.char_per_line_help')]); !!}
        </div>
      </div>

      <div class="col-sm-12" id="ip_address_div">
        <div class="form-group">
          {!! html()->label(__('printer.ip_address') . ':*', 'ip_address')->attributes([]) !!}
          {!! html()->text('ip_address', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.ip_address_help')]); !!}
        </div>
      </div>

      <div class="col-sm-12" id="port_div">
        <div class="form-group">
          {!! html()->label(__('printer.port') . ':*', 'port')->attributes([]) !!}
            {!! html()->text('port', 9100)->attributes(['class' => 'form-control', 'required']); !!}
            <span class="help-block">@lang('lang_v1.port_help')</span>
        </div>
      </div>

      <div class="col-sm-12 hide" id="path_div">
        <div class="form-group">
          {!! html()->label(__('printer.path') . ':*', 'path')->attributes([]) !!}
          {!! html()->text('path', null)->attributes(['class' => 'form-control', 'required']); !!}
          <span class="help-block">
            <b>@lang('lang_v1.connection_type_windows'): </b> @lang('lang_v1.windows_type_help') <code>LPT1</code> (parallel) / <code>COM1</code> (serial). <br/>
            <b>@lang('lang_v1.connection_type_linux'): </b> @lang('lang_v1.linux_type_help') <code>/dev/lp0</code> (parallel), <code>/dev/usb/lp1</code> (USB), <code>/dev/ttyUSB0</code> (USB-Serial), <code>/dev/ttyS0</code> (serial). <br/>
          </span>
        </div>
      </div>

      <div class="col-sm-12 text-center">
        <button type="submit" class="btn btn-primary btn-big">@lang('messages.save')</button>
      </div>
    </div>
  @endcomponent
  {!! html()->form()->close() !!}
</section>
<!-- /.content -->
@endsection