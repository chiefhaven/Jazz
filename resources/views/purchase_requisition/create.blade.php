@extends('layouts.app')
@section('title', __('lang_v1.add_purchase_requisition'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.add_purchase_requisition')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\PurchaseRequisitionController::class, 'store']), 'method' => 'post', 'id' => 'add_purchase_requisition_form' ]))->open() !!}
	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			<div class="col-sm-4">
          		<div class="form-group">
            		{!! html()->label(__('product.brand') . ':', 'brand_id')->attributes([]) !!}
              		{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('brand_id[]', $brands, null, ['class' => 'form-control select2', 'multiple', 'id' => 'brand_id'])); !!}
           
          		</div>
        	</div>
        	<div class="col-sm-4 @if(!session('business.enable_category')) hide @endif">
          		<div class="form-group">
            		{!! html()->label(__('product.category') . ':', 'category_id')->attributes([]) !!}
              		{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('category_id[]', $categories, null, ['class' => 'form-control select2', 'multiple', 'id' => 'category_id'])); !!}
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
			<div class="col-sm-4">
				<div class="form-group">
					{!! html()->label(__('purchase.business_location').':', 'location_id')->attributes([]) !!}
					{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 text-right">
				<br>
				<button type="button" class="tw-dw-btn tw-dw-btn-warning tw-text-white tw-dw-btn-sm" id="show_pr_products"><i class="fas fa-search"></i> @lang('lang_v1.show_products')</button>
			</div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
					{!! html()->label(__('purchase.ref_no').':', 'ref_no')->attributes([]) !!}
					@show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
					{!! html()->text('ref_no', null)->attributes(['class' => 'form-control']); !!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					{!! html()->label(__('lang_v1.required_by_date') . ':', 'delivery_date')->attributes([]) !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! html()->text('delivery_date', null)->attributes(['class' => 'form-control', 'readonly']); !!}
					</div>
				</div>
			</div>
		</div>	
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			<div class="col-md-12">
				<table class="table" id="products_list">
					<thead>
						<tr>
							<th width="40%">@lang('sale.product')</th>
							<th width="20%">@lang('product.alert_quantity')</th>
							<th width="35%">@lang('lang_v1.required_quantity')</th>
							<th width="5%"><i class="text-danger fas fa-trash"></i></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	@endcomponent

	<div class="row">
		<div class="col-sm-12 text-center">
			<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white" id="submit_pr_form">@lang('messages.save')</button>
		</div>
	</div>

{!! html()->form()->close() !!}
</section>
@endsection

@section('javascript')
	<script type="text/javascript">
		$(document).ready( function(){
      		__page_leave_confirmation('#add_purchase_requisition_form');
      		$('#delivery_date').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            var data = {
            	location_id: $('#location_id').val(),
            	brand_id: $('#brand_id').val(),
            	category_id: $('#category_id').val()
            }

            $('#show_pr_products').click( function(){
            	if ($('#location_id').val() == '') {
            		alert('{{__("lang_v1.select_location")}}');
            		return false;
            	}
            	var data = {
	            	location_id: $('#location_id').val(),
	            	brand_id: $('#brand_id').val(),
	            	category_id: $('#category_id').val()
	            }

            	$.ajax({
                    method: 'post',
                    url: "{{route('get-requisition-products')}}",
                    dataType: 'html',
                    data: data,
                    success: function(result) {
                    	var rows = $(result);
                    	rows.find('tr').each(function(){
                    		var row_variation_id = $(this).attr('data-variation_id');
                    		if ($('tr[data-variation_id="' + row_variation_id + '"]').length == 0) {
                    			$('#products_list tbody').append($(this));
                    		}
                    	})
                        
                    },
                });
            });
    	});

		var prev_location;

		$('#location_id').on('select2:selecting', function(){
		    prev_location = $(this).val();
		})

		$('#location_id').on('select2:select', function(){
			if ($('#products_list tbody').find('tr').length > 0){
        		swal({
		            title: LANG.sure,
		            text: '{{__("lang_v1.all_added_products_will_be_removed")}}',
		            icon: 'warning',
		            buttons: true,
		            dangerMode: true,
		        }).then(willDelete => {
		            if (willDelete) {
		                $('#products_list tbody').html('');
		            } else {
		        		$('#location_id').val(prev_location);
		        		$('#location_id').change();
		        		return false;
		        	}
		        });
        	}
		});

    	$(document).on('click', 'button.remove_product_line', function(){
    		$(this).closest('tr').remove();
    	})

    	$(document).on('click', 'button#submit_pr_form', function(e){
    		e.preventDefault();
    		if ($('#products_list tbody').find('tr').length == 0){
    			toastr.warning(LANG.no_products_added);
    			return false;
    		}
    		if ($('form#add_purchase_requisition_form').valid()) {
    			$('form#add_purchase_requisition_form').submit();
    		}
    		
    	})
	</script>
@endsection
