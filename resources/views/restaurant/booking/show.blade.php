<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang( 'restaurant.booking_details' )</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<strong>@lang('contact.customer'):</strong> {{ $booking->customer->name }}<br>
						<strong>@lang('restaurant.service_staff'):</strong> {{ $booking->waiter->user_full_name ?? '--' }}<br>
						<strong>@lang('restaurant.correspondent'):</strong> {{ $booking->correspondent->user_full_name ?? '--' }}<br>
						@if(!empty($booking->booking_note))
						<strong>@lang('restaurant.customer_note'):</strong> {{ $booking->booking_note }}
						@endif
					</div>
					<div class="col-sm-6">
						<strong>@lang('messages.location'):</strong> {{ $booking->location->name }}<br>
						<strong>@lang('restaurant.table'):</strong> {{ $booking->table->name ?? '--' }}<br>
						<strong>@lang('restaurant.booking_starts'):</strong> {{ $booking_start }}<br>
						<strong>@lang('restaurant.booking_ends'):</strong> {{ $booking_end }}
					</div>
				</div>
				<br>
				<hr>
				<div class="row">
					<div class="col-sm-12">
						<button type="button" class="btn btn-info btn-modal pull-right" data-href="{{action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $booking->id,'template_for' => 'new_booking'])}}" data-container=".view_modal">@lang('restaurant.send_notification_to_customer')</button>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-9">
						{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\Restaurant\BookingController::class, 'update'], [$booking->id]), 'method' => 'PUT', 'id' => 'edit_booking_form' ]))->open() !!}
							<div class="input-group">
				                <!-- /btn-group -->
				                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('booking_status', $booking_statuses, $booking->booking_status, ['class' => 'form-control', 'placeholder' => __('restaurant.change_booking_status'), 'required'])); !!}
				                <div class="input-group-btn">
				                  <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
				                </div>
				             </div>
						{!! html()->form()->close() !!}
					</div>
					<div class="col-sm-3 text-center">
						<button type="button" class="btn btn-danger" id="delete_booking" data-href="{{action([\App\Http\Controllers\Restaurant\BookingController::class, 'destroy'], [$booking->id])}}">@lang('restaurant.delete_booking')</button>
					</div>
				</div>
			<br>
			<div class="modal-footer">
			<button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
			</div>
		

	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->