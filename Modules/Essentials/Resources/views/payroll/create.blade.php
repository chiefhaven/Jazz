@extends('layouts.app')

@php
    $action_url = action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'store']);
    $title = __( 'essentials::lang.add_payroll' );
    $subtitle = __( 'essentials::lang.add_payroll' );
    $submit_btn_text = __( 'messages.save' );
    $group_name = __('essentials::lang.payroll_for_month', ['date' => $month_name . ' ' . $year]);
    if ($action == 'edit') {
        $action_url = action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'getUpdatePayrollGroup']);
        $title = __( 'essentials::lang.edit_payroll' );
        $subtitle = __( 'essentials::lang.edit_payroll' );
        $submit_btn_text = __( 'messages.update' );
    }
@endphp

@section('title', $title)

@section('content')
@include('essentials::layouts.nav_hrm')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>{{$subtitle}}</h1>
</section>

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => $action_url, 'method' => 'post', 'id' => 'add_payroll_form' ]))->open() !!}
    {!! html()->hidden('transaction_date', $transaction_date)->attributes([]); !!}
    @if($action == 'edit')
        {!! html()->hidden('payroll_group_id', $payroll_group->id)->attributes([]); !!}
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h3>
                                {!! $group_name !!}
                            </h3>
                            <small>
                                <b>@lang('business.location')</b> :
                                @if(!empty($location))
                                    {{$location->name}}
                                    {!! html()->hidden('location_id', $location->id)->attributes([]); !!}
                                @else
                                    {{__('report.all_locations')}}
                                    {!! html()->hidden('location_id', '')->attributes([]); !!}
                                @endif
                            </small>
                        </div>
                        <div class="col-md-4">
                            {!! html()->label(__( 'essentials::lang.payroll_group_name' ) . ':*', 'payroll_group_name')->attributes([]) !!}
                            {!! html()->text('payroll_group_name', !empty($payroll_group) ? $payroll_group->name : strip_tags($group_name))->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.payroll_group_name' ), 'required']); !!}
                        </div>
                        <div class="col-md-4">
                            {!! html()->label(__( 'sale.status' ) . ':*', 'payroll_group_status')->attributes([]) !!}
                            @show_tooltip(__('essentials::lang.group_status_tooltip'))
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('payroll_group_status', ['draft' => __('sale.draft'), 'final' => __('sale.final')], !empty($payroll_group) ? $payroll_group->status : null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;', 'placeholder' => __( 'messages.please_select' )])); !!}
                            <p class="help-block text-muted">@lang('essentials::lang.payroll_cant_be_deleted_if_final')</p>
                        </div>
                    </div><br><br>
                    <table class="table" id="payroll_table">
                        <tr>
                            <th>
                                @lang('essentials::lang.employee')
                            </th>
                            <th>
                                @lang('essentials::lang.salary')
                            </th>
                            <th>
                                @lang('essentials::lang.allowances')
                            </th>
                            <th>
                                @lang('essentials::lang.deductions')
                            </th>
                            <th>
                                @lang('essentials::lang.gross_amount')
                            </th>
                        </tr>
                        @foreach($payrolls as $employee => $payroll)
                            @php
                                if ($action != 'edit') {
                                    $amount_per_unit_duration = (double)$payroll['essentials_salary'];
                                    $total_work_duration = 1;
                                    $duration_unit = __('lang_v1.month');
                                    if ($payroll['essentials_pay_period'] == 'week') {
                                        $total_work_duration = 4;
                                        $duration_unit = __('essentials::lang.week');
                                    } elseif ($payroll['essentials_pay_period'] == 'day') {
                                        $total_work_duration = \Carbon::parse($transaction_date)->daysInMonth;
                                        $duration_unit = __('lang_v1.day');
                                    }
                                    $total = $total_work_duration * $amount_per_unit_duration;
                                } else {
                                    $amount_per_unit_duration = $payroll['essentials_amount_per_unit_duration'];
                                    $total_work_duration = $payroll['essentials_duration'];
                                    $duration_unit = $payroll['essentials_duration_unit'];
                                    $total = $total_work_duration * $amount_per_unit_duration;
                                }
                            @endphp
                            <tr data-id="{{$employee}}">
                                <input type="hidden" name="payrolls[{{$employee}}][expense_for]" value="{{$employee}}">
                                @if($action == 'edit')
                                    {!! html()->hidden('payrolls['.$employee.'][transaction_id]', $payroll['transaction_id'])->attributes([]); !!}
                                @endif
                                <td>
                                    {{$payroll['name']}}
                                    <br><br>
                                    <b>{{__('essentials::lang.leaves')}} :</b>
                                    {{
                                        __('essentials::lang.total_leaves_days', ['total_leaves' => $payroll['total_leaves']])
                                    }}
                                    <br><br>
                                    <b>{{__('essentials::lang.work_duration')}} :</b> 
                                    {{
                                        __('essentials::lang.work_duration_hour', ['duration' => $payroll['total_work_duration']])
                                    }}
                                    <br><br>
                                    <b>
                                        {{__('essentials::lang.attendance')}}:
                                    </b>
                                    {{$payroll['total_days_worked']}} @lang('lang_v1.days')
                                </td>
                                <td>
                                    {!! html()->label(__( 'essentials::lang.total_work_duration' ) . ':*', 'essentials_duration_'.$employee)->attributes([]) !!}
                                    {!! html()->text('payrolls['.$employee.'][essentials_duration]', $total_work_duration)->attributes(['class' => 'form-control input_number essentials_duration', 'placeholder' => __( 'essentials::lang.total_work_duration' ), 'required', 'data-id' => $employee, 'id' => 'essentials_duration_'.$employee]); !!}
                                    <br>

                                    {!! html()->label(__( 'essentials::lang.duration_unit' ) . ':', 'essentials_duration_unit_'.$employee)->attributes([]) !!}
                                    {!! html()->text('payrolls['.$employee.'][essentials_duration_unit]', $duration_unit)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.duration_unit' ), 'data-id' => $employee, 'id' => 'essentials_duration_unit_'.$employee]); !!}

                                    <br>
                                    
                                    {!! html()->label(__( 'essentials::lang.amount_per_unit_duartion' ) . ':*', 'essentials_amount_per_unit_duration_'.$employee)->attributes([]) !!}
                                    {!! html()->text('payrolls['.$employee.'][essentials_amount_per_unit_duration]', $amount_per_unit_duration)->attributes(['class' => 'form-control input_number essentials_amount_per_unit_duration', 'placeholder' => __( 'essentials::lang.amount_per_unit_duartion' ), 'required', 'data-id' => $employee, 'id' => 'essentials_amount_per_unit_duration_'.$employee]); !!}
                                        
                                    <br>
                                    {!! html()->label(__( 'sale.total' ) . ':', 'total_'.$employee)->attributes([]) !!}
                                    {!! html()->text('payrolls['.$employee.'][total]', $total)->attributes(['class' => 'form-control input_number total', 'placeholder' => __( 'sale.total' ), 'data-id' => $employee, 'id' => 'total_'.$employee]); !!}
                                </td>
                                <td>
                                    @component('components.widget')
                                        <table class="table table-condenced allowance_table" id="allowance_table_{{$employee}}" data-id="{{$employee}}">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-5">@lang('essentials::lang.description')</th>
                                                    <th class="col-md-3">@lang('essentials::lang.amount_type')</th>
                                                    <th class="col-md-3">@lang('sale.amount')</th>
                                                    <th class="col-md-1">&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_allowances = 0;
                                                @endphp
                                                @if(!empty($payroll['allowances']))
                                                    @foreach($payroll['allowances']['allowance_names'] as $key => $value)
                                                        @include('essentials::payroll.allowance_and_deduction_row', ['add_button' => $loop->index == 0 ? true : false, 'type' => 'allowance', 'name' => $value, 'value' => $payroll['allowances']['allowance_amounts'][$key], 'amount_type' => $payroll['allowances']['allowance_types'][$key],
                                                        'percent' => $payroll['allowances']['allowance_percents'][$key] ])

                                                        @php
                                                            $total_allowances += $payroll['allowances']['allowance_amounts'][$key];
                                                        @endphp
                                                    @endforeach
                                                @else
                                                    @include('essentials::payroll.allowance_and_deduction_row', ['add_button' => true, 'type' => 'allowance'])
                                                    @include('essentials::payroll.allowance_and_deduction_row', ['type' => 'allowance'])
                                                    @include('essentials::payroll.allowance_and_deduction_row', ['type' => 'allowance'])
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2">@lang('sale.total')</th>
                                                    <td><span id="total_allowances_{{$employee}}" class="display_currency" data-currency_symbol="true">{{$total_allowances}}</span></td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @endcomponent
                                </td>
                                <td>
                                    @component('components.widget')
                                        <table class="table table-condenced deductions_table" id="deductions_table_{{$employee}}" data-id="{{$employee}}">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-5">@lang('essentials::lang.description')</th>
                                                    <th class="col-md-3">@lang('essentials::lang.amount_type')</th>
                                                    <th class="col-md-3">@lang('sale.amount')</th>
                                                    <th class="col-md-1">&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_deductions = 0;
                                                @endphp
                                                @if(!empty($payroll['deductions']))
                                                    @foreach($payroll['deductions']['deduction_names'] as $key => $value)
                                                        @include('essentials::payroll.allowance_and_deduction_row', ['add_button' => $loop->index == 0 ? true : false, 'type' => 'deduction', 'name' => $value, 'value' => $payroll['deductions']['deduction_amounts'][$key], 
                                                        'amount_type' => $payroll['deductions']['deduction_types'][$key], 'percent' => $payroll['deductions']['deduction_percents'][$key]])

                                                        @php
                                                            $total_deductions += $payroll['deductions']['deduction_amounts'][$key];
                                                        @endphp
                                                    @endforeach
                                                @else
                                                    @include('essentials::payroll.allowance_and_deduction_row', ['add_button' => true, 'type' => 'deduction'])
                                                    @include('essentials::payroll.allowance_and_deduction_row', ['type' => 'deduction'])
                                                    @include('essentials::payroll.allowance_and_deduction_row', ['type' => 'deduction'])
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2">@lang('sale.total')</th>
                                                    <td><span id="total_deductions_{{$employee}}" class="display_currency" data-currency_symbol="true">{{$total_deductions}}</span></td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @endcomponent
                                </td>
                                <td>
                                    <strong>
                                        <span id="gross_amount_text_{{$employee}}">0</span>
                                    </strong>
                                    <br>
                                    {!! html()->hidden('payrolls['.$employee.'][final_total]', 0)->attributes(['id' => 'gross_amount_'.$employee, 'class' => 'gross_amount']); !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div class="form-group">
                                        {!! html()->label(__( 'brand.note' ) . ':', 'note_'.$employee)->attributes([]) !!}
                                        {!! html()->textarea('payrolls['.$employee.'][staff_note]', $payroll['staff_note'] ?? null)->attributes(['class' => 'form-control', 'placeholder' => __( 'sale.total' ), 'id' => 'note_'.$employee, 'rows' => 3]); !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! html()->hidden('total_gross_amount', 0)->attributes(['id' => 'total_gross_amount']); !!}
            <button type="submit" class="btn btn-primary pull-right m-8" id="submit_user_button">
                {{$submit_btn_text}}
            </button>
            <div class="form-group pull-right m-8 mt-15">
                <label>
                    {!! html()->checkbox('notify_employee', 0, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'essentials::lang.notify_employee' ) }}
                </label>
            </div>
        </div>
    </div>
{!! html()->form()->close() !!}
@stop
@section('javascript')
@includeIf('essentials::payroll.form_script')
@endsection
