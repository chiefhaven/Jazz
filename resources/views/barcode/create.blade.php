@extends('layouts.app')
@section('title',  __('barcode.add_barcode_setting'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('barcode.add_barcode_setting')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\BarcodeController::class, 'store']), 'method' => 'post', 
'id' => 'add_barcode_settings_form' ]))->open() !!}
	@component('components.widget')
  <div class="row">
    <div class="col-sm-12">
      <div class="form-group">
        {!! html()->label(__('barcode.setting_name') . ':*', 'name')->attributes([]) !!}
          {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required',
          'placeholder' => __('barcode.setting_name')]); !!}
      </div>
    </div>
    <div class="col-sm-12">
      <div class="form-group">
        {!! html()->label(__('barcode.setting_description'), 'description')->attributes([]) !!}
          {!! html()->textarea('description', null)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.setting_description'), 'rows' => 3]); !!}
      </div>
    </div>
    <div class="col-sm-12">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('is_continuous', false, 1)->attributes(['id' => 'is_continuous']); !!} @lang('barcode.is_continuous')</label>
          </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
         {!! html()->label(__('barcode.top_margin') . ' ('. __('barcode.in_in') . '):*', 'top_margin')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
          </span>
          {!! html()->input('number', 'top_margin', 0)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.top_margin'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        {!! html()->label(__('barcode.left_margin') . ' ('. __('barcode.in_in') . '):*', 'left_margin')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
          </span>
          {!! html()->input('number', 'left_margin', 0)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.left_margin'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
      <div class="form-group">
        {!! html()->label(__('barcode.width') . ' ('. __('barcode.in_in') . '):*', 'width')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-text-width" aria-hidden="true"></i>
          </span>
          {!! html()->input('number', 'width', null)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.width'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        {!! html()->label(__('barcode.height') . ' ('. __('barcode.in_in') . '):*', 'height')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-text-height" aria-hidden="true"></i>
          </span>
          {!! html()->input('number', 'height', null)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.height'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
      <div class="form-group">
        {!! html()->label(__('barcode.paper_width') . ' ('. __('barcode.in_in') . '):*', 'paper_width')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-text-width" aria-hidden="true"></i>
          </span>
          {!! html()->input('number', 'paper_width', null)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.paper_width'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="col-sm-6 paper_height_div">
      <div class="form-group">
        {!! html()->label(__('barcode.paper_height') . ' ('. __('barcode.in_in') . '):*', 'paper_height')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-text-height" aria-hidden="true"></i>
          </span>
          {!! html()->input('number', 'paper_height', null)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.paper_height'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        {!! html()->label(__('barcode.stickers_in_one_row') . ':*', 'stickers_in_one_row')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
          </span>
          {!! html()->input('number', 'stickers_in_one_row', null)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.stickers_in_one_row'), 'min' => 1, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
      <div class="form-group">
        {!! html()->label(__('barcode.row_distance') . ' ('. __('barcode.in_in') . '):*', 'row_distance')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-resize-vertical" aria-hidden="true"></span>
          </span>
          {!! html()->input('number', 'row_distance', 0)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.row_distance'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        {!! html()->label(__('barcode.col_distance') . ' ('. __('barcode.in_in') . '):*', 'col_distance')->attributes([]) !!}
         <div class="input-group">
          <span class="input-group-addon">
            <span class="glyphicon glyphicon-resize-horizontal" aria-hidden="true"></span>
          </span>
          {!! html()->input('number', 'col_distance', 0)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.col_distance'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6 stickers_per_sheet_div">
      <div class="form-group">
        {!! html()->label(__('barcode.stickers_in_one_sheet') . ':*', 'stickers_in_one_sheet')->attributes([]) !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-th" aria-hidden="true"></i>
          </span>
          {!! html()->input('number', 'stickers_in_one_sheet', null)->attributes(['class' => 'form-control',
          'placeholder' => __('barcode.stickers_in_one_sheet'), 'min' => 1, 'required']); !!}
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('is_default', null, 1)->attributes([]); !!} @lang('barcode.set_as_default')</label>
          </div>
      </div>
    </div>
    <div class="col-sm-12 text-center">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
    </div>
  </div>
  @endcomponent
  {!! html()->form()->close() !!}
</section>
<!-- /.content -->
@endsection