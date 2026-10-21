@extends('layouts.app')
@section('title',  __('invoice.add_invoice_layout'))

@section('content')
<style type="text/css">



</style>
@php
  $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('invoice.add_invoice_layout')</h1>
</section>

<!-- Main content -->
<section class="content">
{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\InvoiceLayoutController::class, 'store']), 'method' => 'post', 'id' => 'add_invoice_layout_form', 'files' => true]))->open() !!}
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            {!! html()->label(__('invoice.layout_name') . ':*', 'name')->attributes([]) !!}
              {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required',
              'placeholder' => __('invoice.layout_name')]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! html()->label(__('lang_v1.design') . ':*', 'design')->attributes([]) !!}
              <select name="design" id="design_select" class="form-control">
                @foreach($designs as $key => $label)
                  <option value="{{ $key }}" @if($key === 'classic') selected @endif @if(isset($has_number_formatter) && !$has_number_formatter && $key === 'english-arabic') disabled @endif>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
              <span class="help-block">
                @lang('lang_v1.used_for_browser_based_printing')
              </span>
              @if(isset($has_number_formatter) && !$has_number_formatter)
                <p class="help-block" style="color:#a94442;">
                  <i class="fa fa-info-circle"></i>
                  @lang('lang_v1.english_arabic_requires_intl')
                </p>
              @endif
              <small id="english_arabic_message" style="display: none; color: green; font-size: 12px;">
                 <i class="fa fa-info-circle"></i> @lang('lang_v1.english_arabic_design_help')
               </small>
          </div>

          <div class="form-group hide" id="columnize-taxes">
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" required="required" 
              placeholder="tax 1 name"
              disabled>
              @show_tooltip(__('lang_v1.tooltip_columnize_taxes_heading'))
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" placeholder="tax 2 name"
              disabled>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" placeholder="tax 3 name"
              disabled>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" 
              name="table_tax_headings[]" placeholder="tax 4 name"
              disabled>
            </div>
          </div>

        </div>
        <div class="clearfix"></div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="checkbox">
                    <label>
                        {!! html()->checkbox('show_letter_head', false, 1)->attributes(['class' => 'input-icheck', 'id' => 'show_letter_head']); !!} @lang('lang_v1.show_letter_head')</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 letter_head_input hide">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.letter_head') . ':', 'letter_head')->attributes([]) !!}
                    {!! html()->file('letter_head')->attributes(['accept' => 'image/*']); !!}
                    <span class="help-block">@lang('lang_v1.letter_head_help') <br> @lang('lang_v1.invoice_logo_help', ['max_size' => '1 MB'])</span>
                </div>
            </div>
        <div class="clearfix"></div>

        <!-- Logo -->
        <div class="col-sm-6 hide-for-letterhead">
          <div class="form-group">
            {!! html()->label(__('invoice.invoice_logo') . ':', 'logo')->attributes([]) !!}
            {!! html()->file('logo')->attributes(['accept' => 'image/*']); !!}
            <span class="help-block">@lang('lang_v1.invoice_logo_help', ['max_size' => '1 MB'])</span>
          </div>
        </div>
        <div class="col-sm-6 hide-for-letterhead">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_logo', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_logo')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-12 hide-for-letterhead">
          <div class="form-group">
            {!! html()->label(__('invoice.header_text') . ':', 'header_text')->attributes([]) !!}
            {!! html()->textarea('header_text', '')->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.header_text'), 'rows' => 3]); !!}
          </div>
        </div>
      </div>
      <div class="row hide-for-letterhead">
      <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.sub_heading_line', ['_number_' => 1]) . ':', 'sub_heading_line1')->attributes([]) !!}
            {!! html()->text('sub_heading_line1', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 1]) ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.sub_heading_line', ['_number_' => 2]) . ':', 'sub_heading_line2')->attributes([]) !!}
            {!! html()->text('sub_heading_line2', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 2]) ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.sub_heading_line', ['_number_' => 3]) . ':', 'sub_heading_line3')->attributes([]) !!}
            {!! html()->text('sub_heading_line3', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 3]) ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.sub_heading_line', ['_number_' => 4]) . ':', 'sub_heading_line4')->attributes([]) !!}
            {!! html()->text('sub_heading_line4', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 4]) ]); !!}
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.sub_heading_line', ['_number_' => 5]) . ':', 'sub_heading_line5')->attributes([]) !!}
            {!! html()->text('sub_heading_line5', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.sub_heading_line', ['_number_' => 5]) ]); !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="box box-solid">
  <div class="box-body">
    <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.invoice_heading') . ':', 'invoice_heading')->attributes([]) !!}
            {!! html()->text('invoice_heading', 'Invoice')->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.invoice_heading') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.invoice_heading_not_paid') . ':', 'invoice_heading_not_paid')->attributes([]) !!}
            {!! html()->text('invoice_heading_not_paid', null)->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.invoice_heading_not_paid') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.invoice_heading_paid') . ':', 'invoice_heading_paid')->attributes([]) !!}
            {!! html()->text('invoice_heading_paid', null)->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.invoice_heading_paid') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.proforma_heading') . ':', 'proforma_heading')->attributes([]) !!}
            @show_tooltip(__('lang_v1.tooltip_proforma_heading'))
            {!! html()->text('common_settings[proforma_heading]', __('lang_v1.proforma_invoice'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.proforma_heading'), 'id' => 'proforma_heading' ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.quotation_heading') . ':', 'quotation_heading')->attributes([]) !!}
            @show_tooltip(__('lang_v1.tooltip_quotation_heading'))
            {!! html()->text('quotation_heading', __('lang_v1.quotation'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.quotation_heading') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.sales_order_heading') . ':', 'sales_order_heading')->attributes([]) !!}
            {!! html()->text('common_settings[sales_order_heading]', __('lang_v1.sales_order'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.sales_order_heading'), 'id' => 'sales_order_heading' ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.invoice_no_prefix') . ':', 'invoice_no_prefix')->attributes([]) !!}
            {!! html()->text('invoice_no_prefix', __('sale.invoice_no'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.invoice_no_prefix') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.quotation_no_prefix') . ':', 'quotation_no_prefix')->attributes([]) !!}
            {!! html()->text('quotation_no_prefix', __('lang_v1.quotation_no'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.quotation_no_prefix') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.date_label') . ':', 'date_label')->attributes([]) !!}
            {!! html()->text('date_label', __('lang_v1.date'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.date_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.due_date_label') . ':', 'due_date_label')->attributes([]) !!}
            {!! html()->text('common_settings[due_date_label]', __('lang_v1.due_date'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.due_date_label'), 'id' => 'due_date_label' ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[show_due_date]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_due_date')</label>
              </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.date_time_format') . ':', 'date_time_format')->attributes([]) !!}
            {!! html()->text('date_time_format', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.date_time_format') ]); !!} 
              <p class="help-block">{!! __('lang_v1.date_time_format_help') !!}</p>
          </div>
        </div>
      @php
        $sell_custom_field_1_label = !empty($custom_labels['sell']['custom_field_1']) ? $custom_labels['sell']['custom_field_1'] : '';

        $sell_custom_field_2_label = !empty($custom_labels['sell']['custom_field_2']) ? $custom_labels['sell']['custom_field_2'] : '';

        $sell_custom_field_3_label = !empty($custom_labels['sell']['custom_field_3']) ? $custom_labels['sell']['custom_field_3'] : '';

        $sell_custom_field_4_label = !empty($custom_labels['sell']['custom_field_4']) ? $custom_labels['sell']['custom_field_4'] : '';
      @endphp
        @if (!empty($sell_custom_field_1_label))
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[sell_custom_fields1]', false, 1)->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['sell']['custom_field_1'] ?? __('lang_v1.product_custom_field1') }}</label>
            </div>
          </div>
        </div>
        @endif
        @if (!empty($sell_custom_field_2_label))
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[sell_custom_fields2]', false, 1)->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['sell']['custom_field_2'] ?? __('lang_v1.product_custom_field2') }}</label>
            </div>
          </div>
        </div>
        @endif
        @if (!empty($sell_custom_field_3_label))
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[sell_custom_fields3]', false, 1)->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['sell']['custom_field_3'] ?? __('lang_v1.product_custom_field3') }}</label>
            </div>
          </div>
        </div>
        @endif
        @if (!empty($sell_custom_field_4_label))
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[sell_custom_fields4]', false, 1)->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['sell']['custom_field_4'] ?? __('lang_v1.product_custom_field4') }}</label>
            </div>
          </div>
        </div>
        @endif
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.sales_person_label') . ':', 'sales_person_label')->attributes([]) !!}
            {!! html()->text('sales_person_label', null)->attributes(['class' => 'form-control',
            'placeholder' => __('lang_v1.sales_person_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.commission_agent_label') . ':', 'commission_agent_label')->attributes([]) !!}
            {!! html()->text('commission_agent_label', __('lang_v1.commission_agent'))->attributes(['class' => 'form-control',
            'placeholder' => __('lang_v1.commission_agent_label') ]); !!}
          </div>
        </div>

        <div class="clearfix"></div>
        
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_business_name', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_business_name')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_location_name', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_location_name')</label>
              </div>
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_sales_person', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_sales_person')</label>
              </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_commission_agent', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_commission_agent')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-12">
          <h4>@lang('lang_v1.fields_for_customer_details'):</h4>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_customer', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_customer')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.customer_label') . ':', 'customer_label')->attributes([]) !!}
            {!! html()->text('customer_label', __('contact.customer'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.customer_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_client_id', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_client_id')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.client_id_label') . ':', 'client_id_label')->attributes([]) !!}
            {!! html()->text('client_id_label', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.client_id_label') ]); !!}
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.client_tax_label') . ':', 'client_tax_label')->attributes([]) !!}
            {!! html()->text('client_tax_label', null)->attributes(['class' => 'form-control',
            'placeholder' => __('lang_v1.client_tax_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_reward_point', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_reward_point')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('contact_custom_fields[]', false, 'custom_field1')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['contact']['custom_field_1'] ?? __('lang_v1.contact_custom_field1') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('contact_custom_fields[]', false, 'custom_field2')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['contact']['custom_field_2'] ?? __('lang_v1.contact_custom_field2') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('contact_custom_fields[]', false, 'custom_field3')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['contact']['custom_field_3'] ?? __('lang_v1.contact_custom_field3') }}</label>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('contact_custom_fields[]', false, 'custom_field4')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['contact']['custom_field_4'] ?? __('lang_v1.contact_custom_field4') }}</label>
          </div>
        </div>
      </div>        
    </div>
    <div class="row hide-for-letterhead">
        <div class="col-sm-12">
          <h4>@lang('invoice.fields_to_be_shown_in_address'):</h4>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_landmark', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('business.landmark')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_city', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('business.city')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_state', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('business.state')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_country', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('business.country')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_zip_code', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('business.zip_code')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('location_custom_fields[]', false, 'custom_field1')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['location']['custom_field_1'] ?? __('lang_v1.location_custom_field1') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('location_custom_fields[]', false, 'custom_field2')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['location']['custom_field_2'] ?? __('lang_v1.location_custom_field2') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('location_custom_fields[]', false, 'custom_field3')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['location']['custom_field_3'] ?? __('lang_v1.location_custom_field3') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('location_custom_fields[]', false, 'custom_field4')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['location']['custom_field_4'] ?? __('lang_v1.location_custom_field4') }}</label>
          </div>
        </div>
      </div>

        <div class="clearfix"></div>
         <!-- Shop Communication details -->
        <div class="col-sm-12">
          <h4>@lang('invoice.fields_to_shown_for_communication'):</h4>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_mobile_number', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_mobile_number')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_alternate_number', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_alternate_number')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_email', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_email')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-12">
          <h4>@lang('invoice.fields_to_shown_for_tax'):</h4>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_tax_1', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_tax_1')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_tax_2', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_tax_2')</label>
              </div>
          </div>
        </div>
        
    </div>
    </div>
  </div>
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.product_label') . ':', 'table_product_label')->attributes([]) !!}
            {!! html()->text('table_product_label', __('sale.product'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.product_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.qty_label') . ':', 'table_qty_label')->attributes([]) !!}
            {!! html()->text('table_qty_label', __('lang_v1.quantity'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.qty_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.unit_price_label') . ':', 'table_unit_price_label')->attributes([]) !!}
            {!! html()->text('table_unit_price_label', __('sale.unit_price'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.unit_price_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.subtotal_label') . ':', 'table_subtotal_label')->attributes([]) !!}
            {!! html()->text('table_subtotal_label', __('sale.subtotal'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.subtotal_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.cat_code_label') . ':', 'cat_code_label')->attributes([]) !!}
            {!! html()->text('cat_code_label', 'HSN')->attributes(['class' => 'form-control',
              'placeholder' => 'HSN or Category Code' ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.total_quantity_label') . ':', 'total_quantity_label')->attributes([]) !!}
            {!! html()->text('common_settings[total_quantity_label]', 'Total Quantity')->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.total_quantity_label'), 'id' => 'total_quantity_label' ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.item_discount_label') . ':', 'item_discount_label')->attributes([]) !!}
            {!! html()->text('common_settings[item_discount_label]', 'Discount')->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.item_discount_label'), 'id' => 'item_discount_label' ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.discounted_unit_price_label') . ':', 'discounted_unit_price_label')->attributes([]) !!}
            {!! html()->text('common_settings[discounted_unit_price_label]', 'Price after discount')->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.discounted_unit_price_label'), 'id' => 'discounted_unit_price_label' ]); !!}
          </div>
        </div>
        
        <div class="col-sm-12">
          <h4>@lang('lang_v1.product_details_to_be_shown'):</h4>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_brand', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_brand')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_sku', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_sku')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_cat_code', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_cat_code')</label>
              </div>
          </div>
        </div>
        
        

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_sale_description', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_sale_description')</label>
            </div>
            <p class="help-block">@lang('lang_v1.product_imei_or_sn')</p>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[show_product_description]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_product_description')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('product_custom_fields[]', false, 'product_custom_field1')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['product']['custom_field_1'] ?? __('lang_v1.product_custom_field1') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('product_custom_fields[]', false, 'product_custom_field2')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['product']['custom_field_2'] ?? __('lang_v1.product_custom_field2') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('product_custom_fields[]', false, 'product_custom_field3')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['product']['custom_field_3'] ?? __('lang_v1.product_custom_field3') }}</label>
          </div>
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! html()->checkbox('product_custom_fields[]', false, 'product_custom_field4')->attributes(['class' => 'input-icheck']); !!} {{ $custom_labels['product']['custom_field_4'] ?? __('lang_v1.product_custom_field4') }}</label>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
        @if(request()->session()->get('business.enable_product_expiry') == 1)
          <div class="col-sm-3">
            <div class="form-group">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('show_expiry', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_product_expiry')</label>
                </div>
            </div>
          </div>
        @endif
        @if(request()->session()->get('business.enable_lot_number') == 1)
          <div class="col-sm-3">
            <div class="form-group">
              <div class="checkbox">
                <label>
                  {!! html()->checkbox('show_lot', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_lot_number')</label>
                </div>
            </div>
          </div>
        @endif

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_image', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_product_image')</label>
              </div>
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[show_warranty_name]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_warranty_name')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[show_warranty_exp_date]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_warranty_exp_date')</label>
              </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[show_warranty_description]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_warranty_description')</label>
              </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[show_base_unit_details]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_base_unit_details')</label>
              </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.sub_total_label') . ':', 'sub_total_label')->attributes([]) !!}
            {!! html()->text('sub_total_label', __('sale.subtotal'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.sub_total_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.discount_label') . ':', 'discount_label')->attributes([]) !!}
            {!! html()->text('discount_label', __('sale.discount'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.discount_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.tax_label') . ':', 'tax_label')->attributes([]) !!}
            {!! html()->text('tax_label', __('sale.tax'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.tax_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.total_label') . ':', 'total_label')->attributes([]) !!}
            {!! html()->text('total_label', __('sale.total'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.total_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.total_items_label') . ':', 'total_items_label')->attributes([]) !!}
            {!! html()->text('common_settings[total_items_label]', null)->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.total_items_label'), 'id' => 'total_items_label' ]); !!}
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.round_off_label') . ':', 'round_off_label')->attributes([]) !!}
            {!! html()->text('round_off_label', __('lang_v1.round_off'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.round_off_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.total_due_label') . ' (' . __('lang_v1.current_sale') . '):', 'total_due_label')->attributes([]) !!}
            {!! html()->text('total_due_label', __('report.total_due'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.total_due_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('invoice.paid_label') . ':', 'paid_label')->attributes([]) !!}
            {!! html()->text('paid_label', __('sale.total_paid'))->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.paid_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_payments', true, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_payments')</label>
              </div>
          </div>
        </div>
        <!-- Barcode -->
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_barcode', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('invoice.show_barcode')</label>
              </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('invoice.total_due_label') . ' (' . __('lang_v1.all_sales') . '):', 'prev_bal_label')->attributes([]) !!}
            {!! html()->text('prev_bal_label', '')->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.total_due_label') ]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_previous_bal', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_previous_bal_due')</label>
                @show_tooltip(__('lang_v1.previous_bal_due_help'))
              </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__('lang_v1.previous_balance_due') . ':', 'previous_balance_due_label')->attributes([]) !!}
            {!! html()->text('previous_balance_due_label', null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.previous_balance_due')]); !!}
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('show_previous_balance_due', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_previous_balance_due')</label>
                @show_tooltip(__('lang_v1.previous_balance_due_help'))
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.change_return_label') . ':', 'change_return_label')->attributes([]) !!} @show_tooltip(__('lang_v1.change_return_help'))
            {!! html()->text('change_return_label', __('lang_v1.change_return'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.change_return_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3 hide" id="hide_price_div">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('common_settings[hide_price]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.hide_all_prices')</label>
              </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
              <label>
                {!! html()->checkbox('common_settings[show_total_in_words]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_total_in_words')</label> @show_tooltip(__('lang_v1.show_in_word_help'))
                @if(!extension_loaded('intl'))
                  <p class="help-block">@lang('lang_v1.enable_php_intl_extension')</p>
                @endif
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.word_format') . ':', 'word_format')->attributes([]) !!} 
            @show_tooltip(__('lang_v1.word_format_help'))
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('common_settings[num_to_word_format]', ['international' => __('lang_v1.international'), 'indian' => __('lang_v1.indian')], 'international', ['class' => 'form-control', 'id' => 'word_format'])); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.tax_summary_label') . ':', 'tax_summary_label')->attributes([]) !!}
            {!! html()->text('common_settings[tax_summary_label]', '')->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.tax_summary_label'), 'id' => 'tax_summary_label' ]); !!}
          </div>
        </div>

        


      </div>
    </div>
  </div>
	<div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6 hide">
          <div class="form-group">
            {!! html()->label(__('invoice.highlight_color') . ':', 'highlight_color')->attributes([]) !!}
            {!! html()->text('highlight_color', '#000000')->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.highlight_color') ]); !!}
          </div>
        </div>
        
        <div class="clearfix"></div>
        <div class="col-md-12 hide">
          <hr/>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__('invoice.footer_text') . ':', 'footer_text')->attributes([]) !!}
              {!! html()->textarea('footer_text', null)->attributes(['class' => 'form-control',
              'placeholder' => __('invoice.footer_text'), 'rows' => 3]); !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <br>
            <div class="checkbox">
              <label>
                {!! html()->checkbox('is_default', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('barcode.set_as_default')</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @component('components.widget', ['class' => 'box-solid', 'title' => __('lang_v1.qr_code')])
  <div class="row">
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('show_qr_code', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_qr_code')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('common_settings[show_qr_code_label]', false, 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.show_labels')</label>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('common_settings[zatca_qr]', false, 1)->attributes(['class' => 'input-icheck', 'id' => 'zatca_qr']); !!} @lang('lang_v1.zatca_qr')</label>
                @show_tooltip(__('lang_v1.zatca_qr_help'))
            </div>
        </div>
    </div>
    <div class="col-sm-4" id="zatca_phase_container" style="display: none;">
      <div class="form-group">
        {!! html()->label(__('lang_v1.zatca_phase') . ':*', 'zatca_phase')->attributes([]) !!}
          {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('common_settings[zatca_phase]', [
            'phase_1' => __('lang_v1.zatca_phase1'),
            'phase_2' => __('lang_v1.zatca_phase2'),
          ], $invoice_layout->common_settings['zatca_phase'] ?? null, ['class' => 'form-control', 'id' => 'zatca_phase'])); !!}
            <small id="phase2_message" style="display: none; color: green; font-size: 12px;">
              {{ __('lang_v1.phase2_message') }}
          </small>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
      <h4>@lang('lang_v1.fields_to_be_shown'):</h4>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'business_name')->attributes(['class' => 'input-icheck']); !!} @lang('business.business_name')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'address')->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.business_location_address')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'tax_1')->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.business_tax_1')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'tax_2')->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.business_tax_2')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'invoice_no')->attributes(['class' => 'input-icheck']); !!} @lang('sale.invoice_no')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'invoice_datetime')->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.invoice_datetime')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'subtotal')->attributes(['class' => 'input-icheck']); !!} @lang('sale.subtotal')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'total_amount')->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.total_amount_with_tax')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'total_tax')->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.total_tax')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'customer_name')->attributes(['class' => 'input-icheck']); !!} @lang('sale.customer_name')</label>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        <div class="checkbox">
          <label>
            {!! html()->checkbox('qr_code_fields[]', false, 'invoice_url')->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.view_invoice_url')</label>
        </div>
      </div>
    </div>
  </div>
  @endcomponent

  @if(!empty($enabled_modules) && in_array('types_of_service', $enabled_modules) )
    @include('types_of_service.invoice_layout_settings')
  @endif
  
  <!-- Call restaurant module if defined -->
  @include('restaurant.partials.invoice_layout')

  @if(Module::has('Repair'))
    @include('repair::layouts.partials.invoice_layout_settings')
  @endif
  <div class="box box-solid">
    <div class="box-header with-border">
      <h3 class="box-title">@lang('lang_v1.layout_credit_note')</h3>
    </div>

    <div class="box-body">
      <div class="row">
        
        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.cn_heading') . ':', 'cn_heading')->attributes([]) !!}
            {!! html()->text('cn_heading', 'Credit Note')->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.cn_heading') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.cn_no_label') . ':', 'cn_no_label')->attributes([]) !!}
            {!! html()->text('cn_no_label', __('purchase.ref_no'))->attributes(['class' => 'form-control',
              'placeholder' => __('lang_v1.cn_no_label') ]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! html()->label(__('lang_v1.cn_amount_label') . ':', 'cn_amount_label')->attributes([]) !!}
            {!! html()->text('cn_amount_label', 'Credit Amount')->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.cn_amount_label') ]); !!}
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12 text-center">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg">@lang('messages.save')</button>
    </div>
  </div>

  {!! html()->form()->close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
  __page_leave_confirmation('#add_invoice_layout_form');
    $(document).on('ifChanged', '#show_letter_head', function() {
        letter_head_changed();
    });

    function letter_head_changed() {
        if($('#show_letter_head').is(":checked")) {
            $('.hide-for-letterhead').addClass('hide');
            $('.letter_head_input').removeClass('hide');
        } else {
            $('.hide-for-letterhead').removeClass('hide');
            $('.letter_head_input').addClass('hide');
        }
    }
</script>
<script>
  $(document).ready(function () {
      function toggleZatcaPhase() {
          if ($('#zatca_qr').is(':checked')) {
              $('#zatca_phase_container').fadeIn();
          } else {
              $('#zatca_phase_container').fadeOut();
          }
      }

      function checkPhaseSelection(){
        if ($('#zatca_phase').val() === 'phase_2') {
            $('#phase2_message').fadeIn();
        } else {
            $('#phase2_message').fadeOut();
        }
      }

      function toggleEnglishArabicMessage() {
          if ($('#design_select').val() === 'english-arabic') {
              $('#english_arabic_message').fadeIn();
          } else {
              $('#english_arabic_message').fadeOut();
          }
      }

      // $(document).on('change', '#zatca_qr', function () {
      //       toggleZatcaPhase();
      // });

      $('#zatca_qr').on('ifChanged', function(event){
        //Check if checkbox is checked or not
        toggleZatcaPhase()
      });

      $('#zatca_phase').on('change', function () {
        checkPhaseSelection();
      });

      $('#design_select').on('change', function () {
        toggleEnglishArabicMessage();
      });

      // Check on page load
      toggleEnglishArabicMessage();
  });
</script>
@endsection