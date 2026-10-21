@extends('layouts.app')
@section('title', __('lang_v1.items_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('lang_v1.items_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->label(__('purchase.supplier') . ':', 'ir_supplier_id')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('ir_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->label(__('purchase.purchase_date') . ':', 'ir_purchase_date_filter')->attributes([]) !!}
                    {!! html()->text('ir_purchase_date_filter', null)->attributes(['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->label(__('contact.customer') . ':', 'ir_customer_id')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('ir_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.sell_date') . ':', 'ir_sale_date_filter')->attributes([]) !!}
                    {!! html()->text('ir_sale_date_filter', null)->attributes(['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->label(__('purchase.business_location').':', 'ir_location_id')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('ir_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select'), 'required'])); !!}
                    </div>
                </div>
            </div>
            @if(Module::has('Manufacturing'))
                <div class="col-md-3">
                    <div class="form-group">
                        <br>
                        <div class="checkbox">
                            <label>
                              {!! html()->checkbox('only_mfg', false, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'only_mfg_products']); !!} {{ __('manufacturing::lang.only_mfg_products') }}
                            </label>
                        </div>
                    </div>
                </div>
            @endif
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" 
                    id="items_report_table">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>@lang('product.sku')</th>
                                <th>@lang('lang_v1.description')</th>
                                <th>@lang('purchase.purchase_date')</th>
                                <th>@lang('lang_v1.purchase')</th>
                                <th>@lang('lang_v1.lot_number')</th>
                                <th>@lang('purchase.supplier')</th>
                                <th>@lang('lang_v1.purchase_price')</th>
                                <th>@lang('lang_v1.sell_date')</th>
                                <th>@lang('business.sale')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('sale.location')</th>
                                <th>@lang('lang_v1.sell_quantity')</th>
                                <th>@lang('lang_v1.selling_price')</th>
                                <th>@lang('sale.subtotal')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 text-center footer-total">
                                <td colspan="7"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_pp" 
                                    class="display_currency" data-currency_symbol="true"></td>
                                <td colspan="4"></td>
                                <td id="footer_total_qty"></td>
                                <td id="footer_total_sp"
                                    class="display_currency" data-currency_symbol="true"></td>
                                <td id="footer_total_subtotal"
                                    class="display_currency" data-currency_symbol="true"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection