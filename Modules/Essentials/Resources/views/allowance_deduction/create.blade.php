<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController::class, 'store']), 'method' => 'post', 'id' => 'add_allowance_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.add_pay_component' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
	        	{!! html()->label(__( 'lang_v1.description' ) . ':*', 'description')->attributes([]) !!}
	          	{!! html()->text('description', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.description' ) ]); !!}
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! html()->label(__( 'lang_v1.type' ) . ':*', 'type')->attributes([]) !!}
	          	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('type', ['allowance' => __('essentials::lang.allowance'), 'deduction' => __('essentials::lang.deduction')], 'allowance', ['class' => 'form-control', 'required' ])); !!}
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! html()->label(__('essentials::lang.employee') . ':', 'employees')->attributes([]) !!}
	          	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('employees[]', $users, null, ['class' => 'form-control select2', 'multiple' ])); !!}
	      	</div>

	      	<div class="form-group col-md-6">
	        	{!! html()->label(__( 'essentials::lang.amount_type' ) . ':*', 'amount_type')->attributes([]) !!}
	          	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('amount_type', ['fixed' => __('lang_v1.fixed'), 'percent' => __('lang_v1.percentage')], 'fixed', ['class' => 'form-control', 'required' ])); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-6">
	        	{!! html()->label(__( 'sale.amount' ) . ':*', 'amount')->attributes([]) !!}
	          	{!! html()->text('amount', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'sale.amount' ), 'required' ]); !!}
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! html()->label(__( 'essentials::lang.applicable_date' ) . ':', 'applicable_date')->attributes([]) !!} @show_tooltip(__('essentials::lang.applicable_date_help'))
	        	<div class="input-group data">
	        		{!! html()->text('applicable_date', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'essentials::lang.applicable_date' ), 'readonly' ]); !!}
	        		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	        	</div>
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