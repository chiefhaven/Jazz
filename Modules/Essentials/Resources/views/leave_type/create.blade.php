<div class="modal fade" id="add_leave_type_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController::class, 'store']), 'method' => 'post', 'id' => 'add_leave_type_form' ]))->open() !!}

	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title">@lang( 'essentials::lang.add_leave_type' )</h4>
	    </div>

	    <div class="modal-body">
	      	<div class="form-group">
	        	{!! html()->label(__( 'essentials::lang.leave_type' ) . ':*', 'leave_type')->attributes([]) !!}
	          	{!! html()->text('leave_type', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'essentials::lang.leave_type' ) ]); !!}
	      	</div>

	      	<div class="form-group">
	        	{!! html()->label(__( 'essentials::lang.max_leave_count' ) . ':', 'max_leave_count')->attributes([]) !!}
	          	{!! html()->input('number', 'max_leave_count', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.max_leave_count' ) ]); !!}
	      	</div>

	      	<div class="form-group">
	      		<strong>@lang('essentials::lang.leave_count_interval')</strong><br>
	      		<label class="radio-inline">
	      			{!! html()->radio('leave_count_interval', false, ('month' === null ? null : (string) 'month'))->attribute('id', 'leave_count_interval')->attributes([]); !!} @lang('essentials::lang.current_month')
	      		</label>
	      		<label class="radio-inline">
	      			{!! html()->radio('leave_count_interval', false, ('year' === null ? null : (string) 'year'))->attribute('id', 'leave_count_interval')->attributes([]); !!} @lang('essentials::lang.current_fy')
	      		</label>
	      		<label class="radio-inline">
	      			{!! html()->radio('leave_count_interval', false, (null === null ? null : (string) null))->attribute('id', 'leave_count_interval')->attributes([]); !!} @lang('lang_v1.none')
	      		</label>
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