<div class="modal fade" id="clock_in_clock_out_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'clockInClockOut']), 'method' => 'post', 'id' => 'clock_in_clock_out_form' ]))->open() !!}
	    <div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title"><span id="clock_in_text">@lang( 'essentials::lang.clock_in' )</span>
	      	<span id="clock_out_text">@lang( 'essentials::lang.clock_out' )</span></h4>
	    </div>
	    <div class="modal-body">
	    	<div class="row">
	    		<input type="hidden" name="type" id="type">
		      	<div class="form-group col-md-12">
		      		<strong>@lang( 'essentials::lang.ip_address' ): {{$ip_address}}</strong>
		      	</div>
		      	<div class="form-group col-md-12 clock_in_note @if(!empty($clock_in)) hide @endif">
		        	{!! html()->label(__( 'essentials::lang.clock_in_note' ) . ':', 'clock_in_note')->attributes([]) !!}
		        	{!! html()->textarea('clock_in_note', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_note'), 'rows' => 3 ]); !!}
		      	</div>
		      	<div class="form-group col-md-12 clock_out_note @if(empty($clock_in)) hide @endif">
		        	{!! html()->label(__( 'essentials::lang.clock_out_note' ) . ':', 'clock_out_note')->attributes([]) !!}
		        	{!! html()->textarea('clock_out_note', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_note'), 'rows' => 3 ]); !!}
		      	</div>
		      	<input type="hidden" name="clock_in_out_location" id="clock_in_out_location" value="">
	    	</div>
	    	@if($is_location_required)
		    	<div class="row">
		    		<div class="col-md-12">
		    			<b>@lang('messages.location'):</b> <button type="button" class="btn btn-primary btn-xs" id="get_current_location"> <i class="fas fa-map-marker-alt"></i> @lang('essentials::lang.get_current_location')</button>
		    			<br><span class="clock_in_out_location"></span>
		    		</div>
		    		<div class="col-md-12 ask_location" style="display: none;">
		    			<span class="location_required error"></span>
		    			{{-- <button type="button" class="btn btn-sm btn-primary allow_location">
		    				<i class="fas fa-map-marker"></i>
		    				@lang('essentials::lang.allow_location')
		    			</button> --}}
		    		</div>
		    	</div>
		    @endif
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>

	    {!! html()->form()->close() !!}

	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
        	
</div>