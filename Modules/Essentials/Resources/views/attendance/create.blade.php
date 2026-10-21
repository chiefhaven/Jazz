<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'store']), 'method' => 'post', 'id' => 'attendance_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.add_latest_attendance' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
		        {!! html()->label(__('essentials::lang.select_employee') . ':', 'employee')->attributes([]) !!}
		        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('employee', $employees, null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'select_employee', 'placeholder' => __('essentials::lang.select_employee') ])); !!}
    		</div>
    		<table class="table" id="employee_attendance_table">
    			<thead>
    				<th width="10%">@lang('essentials::lang.employee')</th>
    				<th width="15%">@lang('essentials::lang.clock_in_time')</th>
    				<th width="15%">@lang('essentials::lang.clock_out_time')</th>
    				<th width="15%">@lang('essentials::lang.shift')</th>
    				<th width="12%">@lang('essentials::lang.ip_address')</th>
    				<th width="15%">@lang('essentials::lang.clock_in_note')</th>
    				<th width="15%">@lang('essentials::lang.clock_out_note')</th>
    				<th width="3%">&nbsp;</th>
    			</thead>
    			<tbody>
    			</tbody>
    		</table>
    	</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->