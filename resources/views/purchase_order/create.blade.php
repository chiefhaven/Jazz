@extends('layouts.app')
@section('title', __('lang_v1.add_purchase_order'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.add_purchase_order') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\PurchaseOrderController::class, 'store']), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]))->open() !!}
	@component('components.widget', ['class' => 'box-solid'])
		<input type="hidden" id="is_purchase_order">
		<div class="row">
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! html()->label(__('purchase.supplier') . ':*', 'supplier_id')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id'])); !!}
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
				<strong>
					@lang('business.address'):
				</strong>
				<div id="supplier_address_div"></div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! html()->label(__('purchase.ref_no').':', 'ref_no')->attributes([]) !!}
					{!! html()->text('ref_no', null)->attributes(['class' => 'form-control']); !!}
				</div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! html()->label(__('lang_v1.order_date') . ':*', 'transaction_date')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! html()->text('transaction_date', @format_datetime('now'))->attributes(['class' => 'form-control', 'readonly', 'required']); !!}
					</div>
				</div>
			</div>

			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! html()->label(__('lang_v1.delivery_date') . ':', 'delivery_date')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! html()->text('delivery_date', null)->attributes(['class' => 'form-control']); !!}
					</div>
				</div>
			</div>
				
			@if(count($business_locations) == 1)
				@php 
					$default_location = current(array_keys($business_locations->toArray()));
					$search_disable = false; 
				@endphp
			@else
				@php $default_location = null;
				$search_disable = true;
				@endphp
			@endif
			<div class="col-sm-3">
				<div class="form-group">
					{!! html()->label(__('purchase.business_location').':*', 'location_id')->attributes([]) !!}
					@show_tooltip(__('tooltip.purchase_location'))
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'])); !!}
				</div>
			</div>

			<!-- Currency Exchange Rate -->
			<div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
				<div class="form-group">
					{!! html()->label(__('purchase.p_exchange_rate') . ':*', 'exchange_rate')->attributes([]) !!}
					@show_tooltip(__('tooltip.currency_exchange_factor'))
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! html()->input('number', 'exchange_rate', $currency_details->p_exchange_rate)->attributes(['class' => 'form-control', 'required', 'step' => 0.001]); !!}
					</div>
					<span class="help-block text-danger">
						@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
					</span>
				</div>
			</div>

			<div class="col-md-3">
		          <div class="form-group">
		            <div class="multi-input">
		              {!! html()->label(__('contact.pay_term') . ':', 'pay_term_number')->attributes([]) !!} @show_tooltip(__('tooltip.pay_term'))
		              <br/>
		              {!! html()->input('number', 'pay_term_number', null)->attributes(['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

		              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('pay_term_type', ['months' => __('lang_v1.months'), 
		              		'days' => __('lang_v1.days')], null, ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type'])); !!}
		            </div>
		        </div>
		    </div>

			<div class="col-sm-3">
                <div class="form-group">
                    {!! html()->label(__('purchase.attach_document') . ':', 'document')->attributes([]) !!}
                    {!! html()->file('document')->attributes(['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                    <p class="help-block">
                    	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                    	@includeIf('components.document_help_text')
                    </p>
                </div>
            </div>
		</div>
		@if(!empty($common_settings['enable_purchase_requisition']))
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					{!! html()->label(__('lang_v1.purchase_requisition').':', 'purchase_requisition_ids')->attributes([]) !!}
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('purchase_requisition_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'purchase_requisition_ids'])); !!}
				</div>
			</div>
		</div>
		@endif
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-search"></i>
						</span>
						{!! html()->text('search_product', null)->attributes(['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' => $search_disable]); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action([\App\Http\Controllers\ProductController::class, 'quickAdd'])}}" 
            	data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
				</div>
			</div>
		</div>
		@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
			}
		@endphp
		<div class="row">
			<div class="col-sm-12">
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
						<thead>
							<tr>
								<th>#</th>
								<th>@lang( 'product.product_name' )</th>
								<th>@lang( 'lang_v1.order_quantity' )</th>
								<th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
								<th>@lang( 'lang_v1.discount_percent' )</th>
								<th>@lang( 'purchase.unit_cost_before_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
								<th>@lang( 'purchase.line_total' )</th>
								<th class="hide">
									@lang( 'lang_v1.profit_margin' )
								</th>
								<th><i class="fa fa-trash" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<hr/>
				<div class="pull-right col-md-5">
					<table class="pull-right col-md-12">
						<tr>
							<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
							</td>
						</tr>
						<tr class="hide">
							<th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_st_before_tax" class="display_currency"></span>
								<input type="hidden" id="st_before_tax_input" value=0>
							</td>
						</tr>
						<tr>
							<th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_subtotal" class="display_currency"></span>
								<!-- This is total before purchase tax-->
								<input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
							</td>
						</tr>
					</table>
				</div>

				<input type="hidden" id="row_count" value="0">
			</div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
	            {!! html()->label(__('sale.shipping_details'), 'shipping_details')->attributes([]) !!}
	            {!! html()->textarea('shipping_details', null)->attributes(['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '3', 'cols'=>'30']); !!}
	        </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
	            {!! html()->label(__('lang_v1.shipping_address'), 'shipping_address')->attributes([]) !!}
	            {!! html()->textarea('shipping_address', null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '3', 'cols'=>'30']); !!}
	        </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				{!!html()->label(__('sale.shipping_charges'), 'shipping_charges')->attributes([])!!}
				<div class="input-group">
				<span class="input-group-addon">
				<i class="fa fa-info"></i>
				</span>
				{!!html()->text('shipping_charges', @num_format(0.00))->attributes(['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-4">
			<div class="form-group">
	            {!! html()->label(__('lang_v1.shipping_status'), 'shipping_status')->attributes([]) !!}
	            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('shipping_status', $shipping_statuses, null, ['class' => 'form-control','placeholder' => __('messages.please_select')])); !!}
	        </div>
		</div>
		<div class="col-md-4">
	        <div class="form-group">
	            {!! html()->label(__('lang_v1.delivered_to') . ':', 'delivered_to')->attributes([]) !!}
	            {!! html()->text('delivered_to', null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.delivered_to')]); !!}
	        </div>
	    </div>
	    @php
	    	$custom_labels = json_decode(session('business.custom_labels'), true);
	        $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

	        $is_shipping_custom_field_1_required = !empty($custom_labels['shipping']['is_custom_field_1_required']) && $custom_labels['shipping']['is_custom_field_1_required'] == 1 ? true : false;

	        $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

	        $is_shipping_custom_field_2_required = !empty($custom_labels['shipping']['is_custom_field_2_required']) && $custom_labels['shipping']['is_custom_field_2_required'] == 1 ? true : false;

	        $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';
	        
	        $is_shipping_custom_field_3_required = !empty($custom_labels['shipping']['is_custom_field_3_required']) && $custom_labels['shipping']['is_custom_field_3_required'] == 1 ? true : false;

	        $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';
	        
	        $is_shipping_custom_field_4_required = !empty($custom_labels['shipping']['is_custom_field_4_required']) && $custom_labels['shipping']['is_custom_field_4_required'] == 1 ? true : false;

	        $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
	        
	        $is_shipping_custom_field_5_required = !empty($custom_labels['shipping']['is_custom_field_5_required']) && $custom_labels['shipping']['is_custom_field_5_required'] == 1 ? true : false;
        @endphp

        @if(!empty($shipping_custom_label_1))
        	@php
        		$label_1 = $shipping_custom_label_1 . ':';
        		if($is_shipping_custom_field_1_required) {
        			$label_1 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! html()->label($label_1, 'shipping_custom_field_1')->attributes([]) !!}
		            {!! html()->text('shipping_custom_field_1', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_2))
        	@php
        		$label_2 = $shipping_custom_label_2 . ':';
        		if($is_shipping_custom_field_2_required) {
        			$label_2 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! html()->label($label_2, 'shipping_custom_field_2')->attributes([]) !!}
		            {!! html()->text('shipping_custom_field_2', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_3))
        	@php
        		$label_3 = $shipping_custom_label_3 . ':';
        		if($is_shipping_custom_field_3_required) {
        			$label_3 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! html()->label($label_3, 'shipping_custom_field_3')->attributes([]) !!}
		            {!! html()->text('shipping_custom_field_3', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_4))
        	@php
        		$label_4 = $shipping_custom_label_4 . ':';
        		if($is_shipping_custom_field_4_required) {
        			$label_4 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! html()->label($label_4, 'shipping_custom_field_4')->attributes([]) !!}
		            {!! html()->text('shipping_custom_field_4', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_5))
        	@php
        		$label_5 = $shipping_custom_label_5 . ':';
        		if($is_shipping_custom_field_5_required) {
        			$label_5 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! html()->label($label_5, 'shipping_custom_field_5')->attributes([]) !!}
		            {!! html()->text('shipping_custom_field_5', null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
		        </div>
		    </div>
        @endif
        <div class="col-md-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.shipping_documents') . ':', 'shipping_documents')->attributes([]) !!}
                {!! html()->file('shipping_documents[]')->attributes(['id' => 'shipping_documents', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                <p class="help-block">
                	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                	@includeIf('components.document_help_text')
                </p>
            </div>
        </div>        
	</div>
	<div class="row">
			<div class="col-md-12 text-center">
				<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
			</div>
			<div class="col-md-8 col-md-offset-4" id="additional_expenses_div" style="display: none;">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>@lang('lang_v1.additional_expense_name')</th>
							<th>@lang('sale.amount')</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{!! html()->text('additional_expense_key_1', null)->attributes(['class' => 'form-control']); !!}
							</td>
							<td>
								{!! html()->text('additional_expense_value_1', 0)->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! html()->text('additional_expense_key_2', null)->attributes(['class' => 'form-control']); !!}
							</td>
							<td>
								{!! html()->text('additional_expense_value_2', 0)->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! html()->text('additional_expense_key_3', null)->attributes(['class' => 'form-control']); !!}
							</td>
							<td>
								{!! html()->text('additional_expense_value_3', 0)->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! html()->text('additional_expense_key_4', null)->attributes(['class' => 'form-control']); !!}
							</td>
							<td>
								{!! html()->text('additional_expense_value_4', 0)->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-8">
	    {!! html()->hidden('final_total', 0)->attributes(['id' => 'grand_total_hidden']); !!}
		<b>@lang('lang_v1.order_total'): </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>0</span>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			<div class="col-sm-12">
			<table class="table">
				<tr class="hide">
					<td class="col-md-3">
						<div class="form-group">
							{!! html()->label(__( 'purchase.discount_type' ) . ':', 'discount_type')->attributes([]) !!}
							{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control select2'])); !!}
						</div>
					</td>
					<td class="col-md-3">
						<div class="form-group">
						{!! html()->label(__( 'purchase.discount_amount' ) . ':', 'discount_amount')->attributes([]) !!}
						{!! html()->text('discount_amount', 0)->attributes(['class' => 'form-control input_number', 'required']); !!}
						</div>
					</td>
					<td class="col-md-3">
						&nbsp;
					</td>
					<td class="col-md-3">
						<b>@lang( 'purchase.discount' ):</b>(-) 
						<span id="discount_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
				<tr class="hide">
					<td>
						<div class="form-group">
						{!! html()->label(__('purchase.purchase_tax') . ':', 'tax_id')->attributes([]) !!}
						<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
							<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
							@foreach($taxes as $tax)
								<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
							@endforeach
						</select>
						{!! html()->hidden('tax_amount', 0)->attributes(['id' => 'tax_amount']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<b>@lang( 'purchase.purchase_tax' ):</b>(+) 
						<span id="tax_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="form-group">
							{!! html()->label(__('purchase.additional_notes'), 'additional_notes')->attributes([]) !!}
							{!! html()->textarea('additional_notes', null)->attributes(['class' => 'form-control', 'rows' => 3]); !!}
						</div>
					</td>
				</tr>

			</table>
			</div>
		</div>
	@endcomponent
	<div class="row">
			<div class="col-sm-12 text-center">
				<button type="button" id="submit_purchase_form" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
			</div>
		</div>

{!! html()->form()->close() !!}
</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
@endsection

@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready( function(){
      		__page_leave_confirmation('#add_purchase_form');
      		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $('#shipping_documents').fileinput({
		        showUpload: false,
		        showPreview: false,
		        browseLabel: LANG.file_browse_label,
		        removeLabel: LANG.remove,
		    });

			if($('#location_id').length){
				$('#location_id').change();
			}
    	});
	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
