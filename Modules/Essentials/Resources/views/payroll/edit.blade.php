@extends('layouts.app')

@section('title', __( 'essentials::lang.edit_payroll' ))

@section('content')
@include('essentials::layouts.nav_hrm')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'essentials::lang.edit_payroll' )</h1>
</section>

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'update'], [$payroll->id]), 'method' => 'put', 'id' => 'add_payroll_form' ]))->open() !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget')
                <div class="col-md-12">
                    <h4>{!! __('essentials::lang.payroll_of_employee', ['employee' => $payroll->transaction_for->user_full_name, 'date' => $month_name . ' ' . $year]) !!} (@lang('purchase.ref_no'): {{$payroll->ref_no}})</h4>
                    <br>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! html()->label(__( 'essentials::lang.total_work_duration' ) . ':*', 'essentials_duration')->attributes([]) !!}
                        {!! html()->text('essentials_duration', $payroll->essentials_duration)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'essentials::lang.total_work_duration' ), 'required' ]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! html()->label(__( 'essentials::lang.duration_unit' ) . ':', 'essentials_duration_unit')->attributes([]) !!}
                        {!! html()->text('essentials_duration_unit', $payroll->essentials_duration_unit)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.duration_unit' ) ]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! html()->label(__( 'essentials::lang.amount_per_unit_duartion' ) . ':*', 'essentials_amount_per_unit_duration')->attributes([]) !!}
                        {!! html()->text('essentials_amount_per_unit_duration', @num_format($payroll->essentials_amount_per_unit_duration))->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'essentials::lang.amount_per_unit_duartion' ), 'required' ]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! html()->label(__( 'sale.total' ) . ':', 'total')->attributes([]) !!}
                        {!! html()->text('total', @num_format($payroll->essentials_duration * $payroll->essentials_amount_per_unit_duration))->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'sale.total' ) ]); !!}
                    </div>
                </div>
            @endcomponent
        </div>
        <div class="col-md-12">
            @component('components.widget')   
                <h4>@lang('essentials::lang.allowances'):</h4>
                <table class="table table-condenced" id="allowance_table">
                    <thead>
                        <tr>
                            <th class="col-md-5">@lang('essentials::lang.allowance')</th>
                            <th class="col-md-3">@lang('essentials::lang.amount_type')</th>
                            <th class="col-md-3">@lang('sale.amount')</th>
                            <th class="col-md-1">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_allowances = 0;
                        @endphp
                        @forelse($allowances['allowance_names'] as $key => $value)
                            @include('essentials::payroll.edit_allowance_and_deduction_row', ['add_button' => $loop->index == 0 ? true : false, 'type' => 'allowance', 'name' => $value, 'value' => $allowances['allowance_amounts'][$key], 'amount_type' => !empty($allowances['allowance_types'][$key]) ? $allowances['allowance_types'][$key] : 'fixed',
                            'percent' => !empty($allowances['allowance_percents'][$key]) ? $allowances['allowance_percents'][$key] : 0])

                            @php
                                $total_allowances += !empty($allowances['allowance_amounts'][$key]) ? $allowances['allowance_amounts'][$key] : 0;
                            @endphp
                        @empty
                            @include('essentials::payroll.edit_allowance_and_deduction_row', ['add_button' => true, 'type' => 'allowance'])
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">@lang('sale.total')</th>
                            <td><span id="total_allowances" class="display_currency" data-currency_symbol="true">{{$total_allowances}}</span></td>
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
                </table>
                @endcomponent
            </div>
            <div class="col-md-12">
            @component('components.widget')
                <h4>@lang('essentials::lang.deductions'):</h4>
                <table class="table table-condenced" id="deductions_table">
                    <thead>
                        <tr>
                            <th class="col-md-5">@lang('essentials::lang.deduction')</th>
                            <th class="col-md-3">@lang('essentials::lang.amount_type')</th>
                            <th class="col-md-3">@lang('sale.amount')</th>
                            <th class="col-md-1">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_deductions = 0;
                        @endphp
                        @forelse($deductions['deduction_names'] as $key => $value)
                            @include('essentials::payroll.edit_allowance_and_deduction_row', ['add_button' => $loop->index == 0 ? true : false, 'type' => 'deduction', 'name' => $value, 'value' => $deductions['deduction_amounts'][$key], 
                            'amount_type' => !empty($deductions['deduction_types'][$key]) ? $deductions['deduction_types'][$key] : 'fixed', 'percent' => !empty($deductions['deduction_percents'][$key]) ? $deductions['deduction_percents'][$key] : 0 ])

                            @php
                                $total_deductions += !empty($deductions['deduction_amounts'][$key]) ? $deductions['deduction_amounts'][$key] : 0;
                            @endphp
                        @empty
                            @include('essentials::payroll.edit_allowance_and_deduction_row', ['add_button' => true, 'type' => 'deduction'])
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">@lang('sale.total')</th>
                            <td><span id="total_deductions" class="display_currency" data-currency_symbol="true">{{$total_deductions}}</span></td>
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
                </table>
             @endcomponent
            </div>
            <div class="col-md-12">
                <h4 class="pull-right">@lang('essentials::lang.gross_amount'): <span id="gross_amount_text" class="display_currency" data-currency_symbol="true">{{$payroll->final_total}}</span></h4>
                {!! html()->hidden('final_total', $payroll->final_total)->attributes(['id' => 'gross_amount']); !!}<br>
            </div>
       
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right" >@lang( 'messages.update' )</button>
        </div>
    </div>
{!! html()->form()->close() !!}
@stop
@section('javascript')
    @includeIf('essentials::payroll.edit_form_script')
@endsection
