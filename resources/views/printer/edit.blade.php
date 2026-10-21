@extends('layouts.app')
@section('title',  __('printer.edit_printer_setting'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('printer.edit_printer_setting')</h1>
</section>

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\PrinterController::class, 'update'], [$printer->id]), 'method' => 'PUT', 
'id' => 'add_printer_form' ]))->open() !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__('printer.name') . ':*', 'name')->attributes([]) !!}
              {!! html()->text('name', $printer->name)->attributes(['class' => 'form-control', 'required',
              'placeholder' => __('lang_v1.printer_name_help')]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__('printer.connection_type') . ':*', 'connection_type')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('connection_type', $connection_types, $printer->connection_type, ['class' => 'form-control select2'])); !!}
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__('printer.capability_profile') . ':*', 'capability_profile')->attributes([]) !!}
            @show_tooltip(__('tooltip.capability_profile'))
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('capability_profile', $capability_profiles, $printer->capability_profile, ['class' => 'form-control select2'])); !!}
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__('printer.character_per_line') . ':*', 'char_per_line')->attributes([]) !!}
              {!! html()->input('number', 'char_per_line', $printer->char_per_line)->attributes(['class' => 'form-control', 'required',
              'placeholder' => __('lang_v1.char_per_line_help')]); !!}
          </div>
        </div>
        
        <div class="col-sm-12" id="ip_address_div">
          <div class="form-group">
            {!! html()->label(__('printer.ip_address') . ':*', 'ip_address')->attributes([]) !!}
              {!! html()->text('ip_address', $printer->ip_address)->attributes(['class' => 'form-control', 'required',
              'placeholder' => __('lang_v1.ip_address_help')]); !!}
          </div>
        </div>

        <div class="col-sm-12" id="port_div">
          <div class="form-group">
            {!! html()->label(__('printer.port') . ':*', 'port')->attributes([]) !!}
              {!! html()->text('port', $printer->port)->attributes(['class' => 'form-control', 'required']); !!}
              <span class="help-block">@lang('lang_v1.port_help')</span>
          </div>
        </div>

        <div class="col-sm-12 hide" id="path_div">
          <div class="form-group">
            {!! html()->label(__('printer.path') . ':*', 'path')->attributes([]) !!}
            {!! html()->text('path', $printer->path)->attributes(['class' => 'form-control', 'required']); !!}

            <span class="help-block">
              <b>Connection Type Windows: </b> The device files will be along the lines of <code>LPT1</code> (parallel) or <code>COM1</code> (serial). <br/>
              <b>Connection Type Linux: </b> Your printer device file will be somewhere like <code>/dev/lp0</code> (parallel), <code>/dev/usb/lp1</code> (USB), <code>/dev/ttyUSB0</code> (USB-Serial), <code>/dev/ttyS0</code> (serial). <br/>
            </span>
          </div>
        </div>

        <div class="col-sm-12 text-center">
          <button type="submit" class="btn btn-primary btn-big">@lang('messages.update')</button>
        </div>
      </div>
    </div>
  </div>
  {!! html()->form()->close() !!}
</section>
<!-- /.content -->
@endsection