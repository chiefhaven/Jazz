<div class="modal fade" id="edit_product_location_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
	    <div class="modal-content">
	    	{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ProductController::class, 'updateProductLocation']), 'method' => 'post', 'id' => 'edit_product_location_form' ]))->open() !!}
		    	<div class="modal-header">
			    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				      <h4 class="modal-title"><span class="add_to_location_title hide">@lang( 'lang_v1.add_location_to_the_selected_products' )</span><span class="remove_from_location_title hide">@lang( 'lang_v1.remove_location_from_the_selected_products' )</span></h4>
			    </div>
			    <div class="modal-body">
			    	<div class="form-group">
		                {!! html()->label(__('purchase.business_location') . ':', 'product_location')->attributes([]) !!}
		                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('product_location[]', $business_locations, null, ['class' => 'form-control', 'style' => 'width:100%', 'required', 'multiple', 'id' => 'product_location'])); !!}
		                {!! html()->hidden('products', null)->attributes(['id' => 'products_to_update_location']) !!}

		                {!! html()->hidden('update_type', null)->attributes(['id' => 'update_type']) !!}
		            </div>
			    </div>
			    <div class="modal-footer">
		      		<button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="update_product_location">@lang( 'messages.save' )</button>
		      		<button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
		    	</div>
	    	{!! html()->form()->close() !!}
	    </div>
    </div>
</div>