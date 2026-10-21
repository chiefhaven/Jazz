<div class="modal-dialog" role="document">
  	<div class="modal-content">
  		{!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => empty($shift) ? action([\Modules\Essentials\Http\Controllers\ShiftController::class, 'store']) : action([\Modules\Essentials\Http\Controllers\ShiftController::class, 'update'], [$shift->id]), 'method' => empty($shift) ? 'post' : 'put', 'id' => 'add_shift_form' ]))->open() !!}
  		<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">@lang( 'essentials::lang.add_shift' )</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="form-group">
	        	{!! html()->label(__( 'user.name' ) . ':*', 'name')->attributes([]) !!}
	        	{!! html()->text('name', !empty($shift->name) ? $shift->name : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'user.name'), 'required']); !!}
	      	</div>
	      	<div class="form-group">
	        	{!! html()->label(__('essentials::lang.shift_type') . ':*', 'type')->attributes([]) !!} @show_tooltip(__('essentials::lang.shift_type_tooltip'))
	        	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('type', ['fixed_shift' => __('essentials::lang.fixed_shift'), 'flexible_shift' => __('essentials::lang.flexible_shift')], !empty($shift->type) ? $shift->type : null, ['class' => 'form-control select2', 'required', 'id' => 'shift_type'])); !!}
	      	</div>
	      	<div class="form-group time_div">
	        	{!! html()->label(__( 'restaurant.start_time' ) . ':*', 'start_time')->attributes([]) !!}
	        	<div class="input-group date">
	        		{!! html()->text('start_time', !empty($shift->start_time) ? @format_time($shift->start_time) : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'restaurant.start_time' ), 'readonly', 'required' ]); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group time_div">
	        	{!! html()->label(__( 'restaurant.end_time' ) . ':*', 'end_time')->attributes([]) !!}
	        	<div class="input-group date">
	        		{!! html()->text('end_time', !empty($shift->end_time) ? @format_time($shift->end_time) : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'restaurant.end_time' ), 'readonly', 'required']); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group">
	        	{!! html()->label(__( 'essentials::lang.holiday' ) . ':', 'holidays')->attributes([]) !!}
	        	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('holidays[]', $days, !empty($shift->holidays) ? $shift->holidays : null, ['class' => 'form-control select2', 'multiple' ])); !!}
	      	</div>
            <div class="form-group">
                <label>
                	{!! html()->checkbox('is_allowed_auto_clockout', !empty($shift->is_allowed_auto_clockout) ? 1 : 0, 1)->attributes(['id' => 'is_allowed_auto_clockout']); !!}
                	@lang('essentials::lang.allow_auto_clockout')
                </label>
                @show_tooltip(__('essentials::lang.allow_auto_clockout_tooltip'))
            </div>
    		<div class="form-group enable_auto_clock_out_time" style="display: none;">
    			{!! html()->label(__( 'essentials::lang.auto_clockout_time' ) . ':*', 'auto_clockout_time')->attributes([]) !!}
	        	<div class="input-group date">
	        		{!! html()->text('auto_clockout_time', !empty($shift->auto_clockout_time) ? @format_time($shift->auto_clockout_time) : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.auto_clockout_time' ), 'readonly', 'required']); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
    		</div>
	    </div>
	    <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! html()->form()->close() !!}
  	</div>
</div>