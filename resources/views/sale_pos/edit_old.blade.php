@extends('layouts.app')

@section('title', 'POS')

@section('content')

<!-- Content Header (Page header) -->
<!-- <section class="content-header">
    <h1>Add Purchase</h1> -->
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
<!-- </section> -->

<!-- Main content -->
<section class="content no-print">
	@if(!empty($pos_settings['allow_overselling']))
		<input type="hidden" id="is_overselling_allowed">
	@endif
	@if(session('business.enable_rp') == 1)
        <input type="hidden" id="reward_point_enabled">
    @endif
	<div class="row">
		<div class="@if(!empty($pos_settings['hide_product_suggestion']) && !empty($pos_settings['hide_recent_trans'])) col-md-10 col-md-offset-1 @else col-md-7 @endif col-sm-12">
			<div class="box box-success">

				<div class="box-header with-border">
					<h3 class="box-title">
						Editing 
						@if($transaction->status == 'draft' && $transaction->is_quotation == 1) 
							@lang('lang_v1.quotation')
						@elseif($transaction->status == 'draft') 
							Draft 
						@elseif($transaction->status == 'final') 
							Invoice 
						@endif 
					<span class="text-success">#{{$transaction->invoice_no}}</span> <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h3>
					<p><strong>@lang('sale.location'):</strong> {{$transaction->location->name}}</p>
					<div class="pull-right box-tools">
                <a class="btn btn-success btn-sm" href="{{action([\App\Http\Controllers\SellPosController::class, 'create'])}}">
                  <strong><i class="fa fa-plus"></i> POS</strong></a>
              </div>
				</div>
				<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
				{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\SellPosController::class, 'update'], [$transaction->id]), 'method' => 'post', 'id' => 'edit_pos_sell_form' ]))->open() !!}

				{{ method_field('PUT') }}

				{!! html()->hidden('location_id', $transaction->location_id)->attributes(['id' => 'location_id', 'data-receipt_printer_type' => !empty($location_printer_type) ? $location_printer_type : 'browser', 'data-default_accounts' => $transaction->location->default_payment_accounts]); !!}

				<!-- /.box-header -->
				<div class="box-body">
					<div class="row">
						@if(!empty($pos_settings['enable_transaction_date']))
						<div class="col-md-4 col-sm-6">
							<div class="form-group">
								{!! html()->label(__('sale.sale_date') . ':*', 'transaction_date')->attributes([]) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
									{!! html()->text('transaction_date', @format_datetime($transaction->transaction_date))->attributes(['class' => 'form-control', 'readonly', 'required']); !!}
								</div>
							</div>
						</div>
						@endif
						@if(config('constants.enable_sell_in_diff_currency') == true)
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fas fa-exchange-alt"></i>
										</span>
										{!! html()->text('exchange_rate', @num_format($transaction->exchange_rate))->attributes(['class' => 'form-control input-sm input_number', 'placeholder' => __('lang_v1.currency_exchange_rate'), 'id' => 'exchange_rate']); !!}
									</div>
								</div>
							</div>
						@endif
						@if(!empty($transaction->selling_price_group_id))
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fas fa-money-bill-alt"></i>
										</span>
										{!! html()->hidden('price_group', $transaction->selling_price_group_id)->attributes(['id' => 'price_group']) !!}
										{!! html()->text('price_group_text', $transaction->price_group->name)->attributes(['class' => 'form-control', 'readonly']); !!}
										<span class="input-group-addon">
										@show_tooltip(__('lang_v1.price_group_help_text'))
									</span> 
									</div>
								</div>
							</div>
						@endif

						@if(in_array('types_of_service', $enabled_modules) && !empty($transaction->types_of_service))
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-external-link text-primary service_modal_btn" ></i>
										</span>
										{!! html()->text('types_of_service_text', $transaction->types_of_service->name)->attributes(['class' => 'form-control', 'readonly']); !!}

										{!! html()->hidden('types_of_service_id', $transaction->types_of_service_id)->attributes(['id' => 'types_of_service_id']) !!}

										<span class="input-group-addon">
											@show_tooltip(__('lang_v1.types_of_service_help'))
										</span> 
									</div>
									<small><p class="help-block @if(empty($transaction->selling_price_group_id)) hide @endif" id="price_group_text">@lang('lang_v1.price_group'): <span>@if(!empty($transaction->selling_price_group_id)){{$transaction->price_group->name}}@endif</span></p></small>
								</div>
							</div>
							<div class="modal fade types_of_service_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
								@if(!empty($transaction->types_of_service))
									@include('types_of_service.pos_form_modal', ['types_of_service' => $transaction->types_of_service])
								@endif
							</div>
						@endif

						@if(in_array('subscription', $enabled_modules))
							<div class="col-md-4 pull-right col-sm-6">
								<div class="checkbox">
									<label>
						              {!! html()->checkbox('is_recurring', $transaction->is_recurring, 1)->attributes(['class' => 'input-icheck', 'id' => 'is_recurring']); !!} @lang('lang_v1.subscribe')?
						            </label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal" class="btn btn-link"><i class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
								</div>
							</div>
						@endif
					</div>
					<div class="row">
						<div class="@if(!empty($commission_agent)) col-sm-4 @else col-sm-6 @endif">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-user"></i>
									</span>
									<input type="hidden" id="default_customer_id" 
									value="{{ $transaction->contact->id }}" >
									<input type="hidden" id="default_customer_name" 
									value="{{ $transaction->contact->name }}" >
									{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('contact_id', [], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required', 'style' => 'width: 100%;'])); !!}
									<span class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
									</span>
								</div>
							</div>
						</div>

						<input type="hidden" name="pay_term_number" id="pay_term_number" value="{{$transaction->pay_term_number}}">
						<input type="hidden" name="pay_term_type" id="pay_term_type" value="{{$transaction->pay_term_type}}">

						@if(!empty($commission_agent))
						<div class="col-sm-4">
							<div class="form-group">
							{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('commission_agent', $commission_agent, $transaction->commission_agent, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.commission_agent')])); !!}
							</div>
						</div>
						@endif
						<div class="@if(!empty($commission_agent)) col-sm-4 @else col-sm-6 @endif">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fa fa-barcode"></i></button>
									</div>
									{!! html()->text('search_product', null)->attributes(['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
									<span class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action([\App\Http\Controllers\ProductController::class, 'quickAdd'])}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
									</span>
								</div>
							</div>
						</div>

						<!-- Call restaurant module if defined -->
				        @if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
				        	<span id="restaurant_module_span" 
				        		data-transaction_id="{{$transaction->id}}">
				          		<div class="col-md-3"></div>
				        	</span>
				        @endif
				     </div>
					<div class="row col-sm-12 pos_product_div">

						<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

						<!-- Keeps count of product rows -->
						<input type="hidden" id="product_row_count" 
							value="{{count($sell_details)}}">
						@php
							$hide_tax = '';
							if( session()->get('business.enable_inline_tax') == 0){
								$hide_tax = 'hide';
							}
						@endphp
						<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
							<thead>
								<tr>
									<th class="text-center @if(!empty($pos_settings['inline_service_staff'])) col-md-3 @else col-md-4 @endif">	
										@lang('sale.product')
									</th>
									<th class="text-center col-md-3">
										@lang('sale.qty')
									</th>
									@if(!empty($pos_settings['inline_service_staff']))
										<th class="text-center col-md-2">
											@lang('restaurant.service_staff')
										</th>
									@endif
									<th class="text-center col-md-2 {{$hide_tax}}">
										@lang('sale.price_inc_tax')
									</th>
									<th class="text-center col-md-3">
										@lang('sale.subtotal')
									</th>
									<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody>

@foreach($sell_details as $sell_line)

	@include('sale_pos.product_row', 
		['product' => $sell_line, 
		'row_count' => $loop->index, 
		'tax_dropdown' => $taxes, 
		'sub_units' => !empty($sell_line->unit_details) ? $sell_line->unit_details : [],
		'action' => 'edit'
	])
@endforeach
							</tbody>
						</table>
					</div>
					@include('sale_pos.partials.pos_details', ['edit' => true])

					@include('sale_pos.partials.payment_modal')

					@if(empty($pos_settings['disable_suspend']))
						@include('sale_pos.partials.suspend_note_modal')
					@endif

					@if(empty($pos_settings['disable_recurring_invoice']))
						@include('sale_pos.partials.recurring_invoice_modal')
					@endif
				</div>
				<!-- /.box-body -->
				{!! html()->form()->close() !!}
			</div>
			<!-- /.box -->
		</div>

		<div class="col-md-5 col-sm-12">
			@include('sale_pos.partials.right_div')
		</div>
	</div>
</section>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
@include('sale_pos.partials.configure_search_modal')

@stop
@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	@include('sale_pos.partials.keyboard_shortcuts')

	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
@endsection

@section('css')
	<style type="text/css">
		/*CSS to print receipts*/
		.print_section{
		    display: none;
		}
		@media print{
		    .print_section{
		        display: block !important;
		    }
		}
		@page {
		    size: 3.1in auto;/* width height */
		    height: auto !important;
		    margin-top: 0mm;
		    margin-bottom: 0mm;
		}
	</style>
@endsection
