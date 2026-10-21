<div class="modal fade" id="add_booking_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

		{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Crm\Http\Controllers\ContactBookingController::class, 'store']), 'method' => 'post', 'id' => 'add_booking_form' ]))->open() !!}
			{!! html()->hidden('contact_id', auth()->user()->crm_contact_id)->attributes([]); !!}
			{!! html()->hidden('booking_status', 'waiting')->attributes([]); !!}
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang( 'restaurant.add_booking' )</h4>
				</div>

				<div class="modal-body">
					@if(count($business_locations) == 1)
						@php 
							$default_location = current(array_keys($business_locations->toArray())) 
						@endphp
					@else
						@php $default_location = null; @endphp
					@endif
					<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-map-marker"></i>
								</span>
								{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $default_location, ['class' => 'form-control', 'placeholder' => __('purchase.business_location'), 'required', 'id' => 'booking_location_id'])); !!}
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6">
						<div class="form-group">
						{!! html()->label(__('restaurant.start_time') . ':*', 'status')->attributes([]) !!}
	            			<div class='input-group date' >
	            			<span class="input-group-addon">
	                    		<span class="glyphicon glyphicon-calendar"></span>
	                		</span>
							{!! html()->text('booking_start', null)->attributes(['class' => 'form-control','placeholder' => __( 'restaurant.start_time' ), 'required', 'id' => 'start_time', 'readonly']); !!}
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							{!! html()->label(__('restaurant.end_time') . ':*', 'status')->attributes([]) !!}
	            			<div class='input-group date' >
	            			<span class="input-group-addon">
	                    		<span class="glyphicon glyphicon-calendar"></span>
	                		</span>
							{!! html()->text('booking_end', null)->attributes(['class' => 'form-control','placeholder' => __( 'restaurant.end_time' ), 'required', 'id' => 'end_time', 'readonly']); !!}
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
						{!! html()->label(__( 'restaurant.customer_note' ) . ':', 'booking_note')->attributes([]) !!}
						{!! html()->textarea('booking_note', null)->attributes(['class' => 'form-control','placeholder' => __( 'restaurant.customer_note' ), 'rows' => 3 ]); !!}
						</div>
					</div>
				</div>

				<div class="modal-footer">
				<button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
			</div>

		{!! html()->form()->close() !!}

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>