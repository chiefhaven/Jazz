<div class="modal fade" id="payroll_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'create']), 'method' => 'get', 'id' => 'add_payroll_step1' ]))->open() !!}

	    <div class="modal-body">
	    	<div class="form-group">
	        	{!! html()->label(__( 'business.location' ) . ':*', 'primary_work_location')->attributes([]) !!}
	          	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('primary_work_location', $locations, null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'primary_work_location'])); !!}
	      	</div>
	      	<div class="form-group">
	        	{!! html()->label(__( 'essentials::lang.employee' ) . ':*', 'employee_ids')->attributes([]) !!}
	        	<button type="button" class="btn btn-primary btn-xs select-all">
                    @lang('lang_v1.select_all')
                </button>
                <button type="button" class="btn btn-primary btn-xs deselect-all">
                    @lang('lang_v1.deselect_all')
                </button>
	          	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('employee_ids[]', $employees, null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;', 'multiple', 'id' => 'employee_ids'])); !!}
	      	</div>

	      	<div class="form-group">
	      		{!! html()->label(__( 'essentials::lang.month_year' ) . ':*', 'month_year')->attributes([]) !!}
	      		<div class="input-group">
	          		{!! html()->text('month_year', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.month_year' ), 'required', 'readonly' ]); !!}
	          		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	      		</div>
	      	</div>
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary">@lang( 'essentials::lang.proceed' )</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>

	    {!! html()->form()->close() !!}

	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>