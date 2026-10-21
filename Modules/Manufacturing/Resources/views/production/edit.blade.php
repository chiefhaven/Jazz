@extends('layouts.app')
@section('title', __('manufacturing::lang.production'))

@section('content')
@include('manufacturing::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('manufacturing::lang.production') </h1>
</section>

<!-- Main content -->
<section class="content">

	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Manufacturing\Http\Controllers\ProductionController::class, 'update'], [$production_purchase->id]), 'method' => 'put', 'id' => 'production_form', 'files' => true ]))->open() !!}
	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					{!! html()->label(__('purchase.ref_no').':', 'ref_no')->attributes([]) !!}
					{!! html()->text('ref_no', $production_purchase->ref_no)->attributes(['class' => 'form-control']); !!}
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					{!! html()->label(__('manufacturing::lang.mfg_date') . ':*', 'transaction_date')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! html()->text('transaction_date', @format_datetime($production_purchase->transaction_date))->attributes(['class' => 'form-control', 'readonly', 'required']); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					{!! html()->label(__('purchase.business_location').':*', 'location_id')->attributes([]) !!}
					@show_tooltip(__('tooltip.purchase_location'))
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $production_purchase->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'])); !!}
				</div>
			</div>
			@php
				$purchase_line = $production_purchase->purchase_lines[0];
			@endphp
			<div class="col-sm-3">
				<div class="form-group">
					{!! html()->label(__('sale.product').':*', 'variation_id_shown')->attributes([]) !!}
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('variation_id_shown', $recipe_dropdown, $purchase_line->variation_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'disabled'])); !!}
					{!! html()->hidden('variation_id', $purchase_line->variation_id)->attributes(['id' => 'variation_id']); !!}
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					{!! html()->label(__('lang_v1.quantity').':*', 'recipe_quantity')->attributes([]) !!}
					<div class="@if(!empty($sub_units)) input_inline @else input-group @endif" id="recipe_quantity_input">
						{!! html()->text('quantity', @format_quantity($quantity))->attributes(['class' => 'form-control input_number', 'id' => 'recipe_quantity', 'required', 'data-rule-notEmpty' => 'true', 'data-rule-notEqualToWastedQuantity' => 'true']); !!}
						<span class="@if(empty($sub_units)) input-group-addon @endif" id="unit_html">
							@if(!empty($sub_units))
								<select name="sub_unit_id" class="form-control" id="sub_unit_id">
								@foreach($sub_units as $key => $value)
									<option 
										value="{{$key}}" 
										data-multiplier="{{$value['multiplier']}}" 
										data-unit_name="{{$value['name']}}"
										@if($key == $sub_unit_id)
											@php
												$unit_name = $value['name'];
											@endphp
											selected
										@endif
										>{{$value['name']}}</option>
								@endforeach
								</select>
							@else
								{{ $unit_name }}
							@endif
						</span>
					</div>
				</div>
			</div>

			<div class="col-md-4">
                <div class="form-group">
                    {!! html()->label(__('purchase.attach_document') . ':', 'upload_document')->attributes([]) !!}
                    {!! html()->file('documents[]')->attributes(['id' => 'upload_document', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                    <p class="help-block">
                    	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                    	@includeIf('components.document_help_text')
                    </p>
                    @include('sell.partials.media_table', ['medias' => $production_purchase->media, 'delete' => true])
                </div>
            </div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid', 'title' => __('manufacturing::lang.ingredients')])
		<div class="row">
			<div class="col-md-12">
				<div id="enter_ingredients_table">
					@include('manufacturing::recipe.ingredients_for_production')
				</div>
			</div>
		</div>
		<div class="row">
			@if(request()->session()->get('business.enable_lot_number') == 1)
				<div class="col-sm-3">
					<div class="form-group">
						{!! html()->label(__('lang_v1.lot_number').':', 'lot_number')->attributes([]) !!}
						{!! html()->text('lot_number', $purchase_line->lot_number)->attributes(['class' => 'form-control']); !!}
					</div>
				</div>
			@endif
			@if(session('business.enable_product_expiry'))
				<div class="col-sm-3">
					<div class="form-group">
						{!! html()->label(__('product.exp_date').':*', 'exp_date')->attributes([]) !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! html()->text('exp_date', !empty($purchase_line->exp_date) ? @format_date($purchase_line->exp_date) : null)->attributes(['class' => 'form-control', 'readonly']); !!}
						</div>
					</div>
				</div>
			@endif
			<div class="col-md-3">
				<div class="form-group">
					{!! html()->label(__('manufacturing::lang.waste_units').':', 'mfg_wasted_units')->attributes([]) !!} @show_tooltip(__('manufacturing::lang.wastage_tooltip'))
					<div class="input-group">
						{!! html()->text('mfg_wasted_units', @num_format($production_purchase->mfg_wasted_units))->attributes(['class' => 'form-control input_number']); !!}
						<span class="input-group-addon" id="wasted_units_text">{{$unit_name}}</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					{!! html()->label(__('manufacturing::lang.production_cost').':', 'production_cost')->attributes([]) !!} @show_tooltip(__('manufacturing::lang.production_cost_tooltip'))
					<div class="input_inline">
						{!! html()->text('production_cost', @num_format($production_purchase->mfg_production_cost))->attributes(['class' => 'form-control input_number']); !!}
						<span>
							{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('mfg_production_cost_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage'), 'per_unit' => __('manufacturing::lang.per_unit')], $production_purchase->mfg_production_cost_type, ['class' => 'form-control', 'id' => 'mfg_production_cost_type'])); !!}	
						</span>
					</div>
					<p><strong>
						{{__('manufacturing::lang.total_production_cost')}}:
					</strong>
					<span id="total_production_cost" class="display_currency" data-currency_symbol="true">{{$total_production_cost}}</span></p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-9">
				{!! html()->hidden('final_total', @num_format($production_purchase->final_total))->attributes(['id' => 'final_total']); !!}
				<strong>
					{{__('manufacturing::lang.total_cost')}}:
				</strong>
				<span id="final_total_text" class="display_currency" data-currency_symbol="true">{{ $production_purchase->final_total }}</span>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-md-offset-9">
				<div class="form-group">
					<br>
					<div class="checkbox">
						<label>
						{!! html()->checkbox('finalize', false, 1)->attributes(['class' => 'input-icheck', 'id' => 'finalize']); !!} @lang('manufacturing::lang.finalize')
						</label> @show_tooltip(__('manufacturing::lang.finalize_tooltip'))
					</div>
		        </div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<button type="submit" class="btn btn-primary pull-right">@lang('messages.submit')</button>
			</div>
		</div>
	@endcomponent

{!! html()->form()->close() !!}
</section>
@endsection

@section('javascript')
	@include('manufacturing::production.production_script')

	<script type="text/javascript">
		$(document).ready( function () {
			calculateRecipeTotal();
		});
	</script>
@endsection
