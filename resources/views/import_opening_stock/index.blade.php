@extends('layouts.app')
@section('title', __('lang_v1.import_opening_stock'))

@section('content')
<br/>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.import_opening_stock')</h1>
</section>

<!-- Main content -->
<section class="content">
    
@if (session('notification') || !empty($notification))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @if(!empty($notification['msg']))
                    {{$notification['msg']}}
                @elseif(session('notification.msg'))
                    {{ session('notification.msg') }}
                @endif
              </div>
          </div>  
      </div>     
@endif
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
                {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ImportOpeningStockController::class, 'store']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]))->open() !!}
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="col-sm-8">
                            <div class="form-group">
                                {!! html()->label(__( 'product.file_to_import' ) . ':', 'name')->attributes([]) !!}
                                @show_tooltip(__('lang_v1.tooltip_import_opening_stock'))
                                {!! html()->file('products_csv')->attributes(['accept'=> '.xls', 'required' => 'required']); !!}
                              </div>
                        </div>
                        <div class="col-sm-4">
                        <br>
                            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.submit')</button>
                        </div>
                        </div>
                    </div>

                {!! html()->form()->close() !!}
                <br><br>
                <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ asset('files/import_opening_stock_csv_template.xls') }}" class="tw-dw-btn tw-dw-btn-success tw-text-white" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.instructions')])
            <strong>@lang('lang_v1.instruction_line1')</strong><br>@lang('lang_v1.instruction_line2')
            <br><br>
            <table class="table table-striped">
                <tr>
                    <th>@lang('lang_v1.col_no')</th>
                    <th>@lang('lang_v1.col_name')</th>
                    <th>@lang('lang_v1.instruction')</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>@lang('product.sku')<small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>@lang('business.location') <small class="text-muted">(@lang('lang_v1.optional')) <br>@lang('lang_v1.location_ins')</small></td>
                    <td>@lang('lang_v1.location_ins1')<br>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>@lang('lang_v1.quantity') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>@lang('purchase.unit_cost_before_tax') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>@lang('lang_v1.lot_number') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td></td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>@lang('lang_v1.expiry_date') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>{!! __('lang_v1.expiry_date_in_business_date_format') !!} <br/> <b>{{$date_format}}</b>, @lang('lang_v1.type'): <b>text</b>, @lang('lang_v1.example'): <b>{{@format_date('today')}}</b></td>
                </tr>
            </table>
        @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection