@extends('accounting::layouts.transactions_layout')
@section('tab-title')
    {{ trans_choice('accounting::general.sales', 1) }} @yield('sub-tab-title')
@endsection

@section('tab-content')

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="@if (Request::get('type') == 'payment') active @endif">
                        <a href="{{ request()->type != 'payment' ? url('accounting/transactions/sales?type=payment') : '#' }}"
                            aria-expanded="true">
                            <i class="fas fa-credit-card" aria-hidden="true"></i>
                            {{ trans_choice('accounting::general.payment', 2) }}
                        </a>
                    </li>
                    <li class="@if (Request::get('type') == 'invoice') active @endif">
                        <a href="{{ request()->type != 'invoice' ? url('accounting/transactions/sales?type=invoice') : '#' }}"
                            aria-expanded="true">
                            <i class="fas fa-file" aria-hidden="true"></i>
                            {{ trans_choice('accounting::general.invoice', 2) }}
                        </a>
                    </li>
                </ul>

                {{-- Filter --}}
                @component('components.filters', ['title' => __('report.filters')])
                    <div class="row">
                        @if (empty($only) || in_array('sell_list_filter_location_id', $only))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! html()->label(__('purchase.business_location') . ':', 'sell_list_filter_location_id')->attributes([]) !!}
                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])) !!}
                                </div>
                            </div>
                        @endif
                        @if ((empty($only) || in_array('sell_list_filter_customer_id', $only)) && Request::get('type') == 'invoice')
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! html()->label(__('contact.customer') . ':', 'sell_list_filter_customer_id')->attributes([]) !!}
                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])) !!}
                                </div>
                            </div>
                        @endif
                        @if (empty($only) || in_array('sell_list_filter_payment_status', $only))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! html()->label(__('purchase.payment_status') . ':', 'sell_list_filter_payment_status')->attributes([]) !!}
                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])) !!}
                                </div>
                            </div>
                        @endif
                        @if (empty($only) || in_array('sell_list_filter_date_range', $only))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! html()->label(__('report.date_range') . ':', 'sell_list_filter_date_range')->attributes([]) !!}
                                    {!! html()->text('sell_list_filter_date_range', null)->attributes(['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
                                </div>
                            </div>
                        @endif
                        @if ((empty($only) || in_array('created_by', $only)) && !empty($sales_representative))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! html()->label(__('report.user') . ':', 'created_by')->attributes([]) !!}
                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%'])) !!}
                                </div>
                            </div>
                        @endif
                        @if (empty($only) || in_array('sales_cmsn_agnt', $only))
                            @if (!empty($is_cmsn_agent_enabled))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! html()->label(__('lang_v1.sales_commission_agent') . ':', 'sales_cmsn_agnt')->attributes([]) !!}
                                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sales_cmsn_agnt', $commission_agents, null, ['class' => 'form-control select2', 'style' => 'width:100%'])) !!}
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if (empty($only) || in_array('service_staffs', $only))
                            @if (!empty($service_staffs))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! html()->label(__('restaurant.service_staff') . ':', 'service_staffs')->attributes([]) !!}
                                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('service_staffs', $service_staffs, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])) !!}
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if (!empty($sources))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! html()->label(__('lang_v1.sources') . ':', 'sell_list_filter_source')->attributes([]) !!}
                                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_list_filter_source', $sources, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                @endcomponent

                <div class="tab-content">
                    <div class="tab-pane active">
                        @yield('sub-tab-content')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('tab-javascript')
    @yield('sub-tab-javascript')
@endsection
