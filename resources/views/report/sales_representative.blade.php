@extends('layouts.app')
@section('title', __('report.sales_representative'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('report.sales_representative')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ReportController::class, 'getStockReport']), 'method' => 'get', 'id' => 'sales_representative_filter_form' ]))->open() !!}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('report.user') . ':', 'sr_id')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sr_id', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('report.all_users')])); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! html()->label(__('business.business_location') . ':', 'sr_business_id')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sr_business_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%'])); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">

                        {!! html()->label(__('report.date_range') . ':', 'sr_date_filter')->attributes([]) !!}
                        {!! html()->text('date_range', null)->attributes(['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'sr_date_filter', 'readonly']); !!}
                    </div>
                </div>

                {!! html()->form()->close() !!}
            @endcomponent
        </div>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['title' => __('report.summary')])
                <h3 class="text-muted">
                    {{ __('report.total_sell') }} - {{ __('lang_v1.total_sales_return') }}: 
                    <span id="sr_total_sales">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                    -
                    <span id="sr_total_sales_return">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                    =
                    <span id="sr_total_sales_final">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                </h3>
                <div class="hide" id="total_payment_with_commsn_div">
                    <h3 class="text-muted">
                        {{ __('lang_v1.total_payment_with_commsn') }}: 
                        <span id="total_payment_with_commsn">
                            <i class="fas fa-sync fa-spin fa-fw"></i>
                        </span>
                    </h3>
                </div>
                <div class="hide" id="total_commission_div">
                    <h3 class="text-muted">
                        {{ __('lang_v1.total_sale_commission') }}: 
                        <span id="sr_total_commission">
                            <i class="fas fa-sync fa-spin fa-fw"></i>
                        </span>
                    </h3>
                </div>
                <h3 class="text-muted">
                    {{ __('report.total_expense') }}: 
                    <span id="sr_total_expenses">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                </h3>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#sr_sales_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cog" aria-hidden="true"></i> @lang('lang_v1.sales_added')</a>
                    </li>

                    <li>
                        <a href="#sr_commission_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cog" aria-hidden="true"></i> @lang('lang_v1.sales_with_commission')</a>
                    </li>

                    <li>
                        <a href="#sr_expenses_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cog" aria-hidden="true"></i> @lang('expense.expenses')</a>
                    </li>

                    @if(!empty($pos_settings['cmmsn_calculation_type']) && $pos_settings['cmmsn_calculation_type'] == 'payment_received')
                        <li>
                            <a href="#sr_payments_with_cmmsn_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cog" aria-hidden="true"></i> @lang('lang_v1.payments_with_cmmsn')</a>
                        </li>
                    @endif
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="sr_sales_tab">
                        @include('report.partials.sales_representative_sales')
                    </div>

                    <div class="tab-pane" id="sr_commission_tab">
                        @include('report.partials.sales_representative_commission')
                    </div>

                    <div class="tab-pane" id="sr_expenses_tab">
                        @include('report.partials.sales_representative_expenses')
                    </div>

                    @if(!empty($pos_settings['cmmsn_calculation_type']) && $pos_settings['cmmsn_calculation_type'] == 'payment_received')
                        <div class="tab-pane" id="sr_payments_with_cmmsn_tab">
                            @include('report.partials.sales_representative_payments_with_cmmsn')
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection