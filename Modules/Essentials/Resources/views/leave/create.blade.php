<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'store']), 'method' => 'post', 'id' => 'add_leave_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.add_leave' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		@can('essentials.crud_all_leave')
    		<div class="form-group col-md-12">
		        {!! html()->label(__('essentials::lang.select_employee') . ':', 'employees')->attributes([]) !!}
		        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('employees[]', $employees, null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'employees', 'multiple', 'required' ])); !!}
    		</div>
    		@endcan
    		<div class="form-group col-md-12">
	        	{!! html()->label(__( 'essentials::lang.leave_type' ) . ':*', 'essentials_leave_type_id')->attributes([]) !!}
	          	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('essentials_leave_type_id', $leave_types, null, ['class' => 'form-control select2', 'required', 'placeholder' => __( 'messages.please_select' ) ])); !!}
	      	</div>

	      	<div class="form-group col-md-6">
	        	{!! html()->label(__( 'essentials::lang.start_date' ) . ':*', 'start_date')->attributes([]) !!}
	        	<div class="input-group data">
	        		{!! html()->text('start_date', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.start_date' ), 'readonly' ]); !!}
	        		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	        	</div>
	      	</div>

	      	<div class="form-group col-md-6">
	        	{!! html()->label(__( 'essentials::lang.end_date' ) . ':*', 'end_date')->attributes([]) !!}
		        	<div class="input-group data">
		          	{!! html()->text('end_date', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.end_date' ), 'readonly', 'required' ]); !!}
		          	<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	        	</div>
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! html()->label(__( 'essentials::lang.reason' ) . ':', 'reason')->attributes([]) !!}
	          	{!! html()->textarea('reason', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.reason' ), 'rows' => 4, 'required' ]); !!}
	      	</div>
	      	<hr>
	      	<div class="col-md-12">
    			{!! $instructions !!}
    		</div>
    	</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary ladda-button add-leave-btn" data-style="expand-right">
      	<span class="ladda-label">@lang( 'messages.save' )</span>
      </button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->