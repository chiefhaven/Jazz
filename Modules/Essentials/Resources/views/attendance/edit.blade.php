<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'update'], [$attendance->id]), 'method' => 'put', 'id' => 'attendance_form' ]))->open() !!}

    {!! html()->hidden('employees', $attendance->employee->id)->attributes(['id' => 'employees']); !!}
    {!! html()->hidden('attendance_id', $attendance->id)->attributes(['id' => 'attendance_id']); !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.edit_attendance' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
    			<strong>@lang('essentials::lang.employees'): </strong> {{$attendance->employee->user_full_name}}
    		</div>
    		<div class="form-group col-md-6">
	        	{!! html()->label(__( 'essentials::lang.clock_in_time' ) . ':*', 'clock_in_time')->attributes([]) !!}
	        	<div class="input-group date">
	        		{!! html()->text('clock_in_time', @format_datetime($attendance->clock_in_time))->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_time' ), 'readonly', 'required' ]); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group col-md-6">
	        	{!! html()->label(__( 'essentials::lang.clock_out_time' ) . ':', 'clock_out_time')->attributes([]) !!}
	        	<div class="input-group date">
	        		{!! html()->text('clock_out_time', !empty($attendance->clock_out_time) ? @format_datetime($attendance->clock_out_time) : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_time' ), 'readonly' ]); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group col-md-6">
	        	{!! html()->label(__( 'essentials::lang.ip_address' ) . ':', 'ip_address')->attributes([]) !!}
	        	{!! html()->text('ip_address', $attendance->ip_address)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.ip_address') ]); !!}
	      	</div>
	      	<div class="form-group col-md-12">
              {!! html()->label(__( 'essentials::lang.clock_in_note' ) . ':', 'clock_in_note')->attributes([]) !!}
              {!! html()->textarea('clock_in_note', $attendance->clock_in_note)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_note'), 'rows' => 3 ]); !!}
            </div>
            <div class="form-group col-md-12">
              {!! html()->label(__( 'essentials::lang.clock_out_note' ) . ':', 'clock_out_note')->attributes([]) !!}
              {!! html()->textarea('clock_out_note', $attendance->clock_out_note)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_note'), 'rows' => 3 ]); !!}
            </div>
    	</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->