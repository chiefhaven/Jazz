<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\CustomerGroupController::class, 'update'], [$customer_group->id]), 'method' => 'PUT', 'id' => 'customer_group_edit_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_customer_group' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! html()->label(__( 'lang_v1.customer_group_name' ) . ':*', 'name')->attributes([]) !!}
        {!! html()->text('name', $customer_group->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.customer_group_name' )]); !!}
      </div>
      <div class="form-group">
            {!! html()->label(__( 'lang_v1.price_calculation_type' ) . ':', 'price_calculation_type')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('price_calculation_type', ['percentage' => __('lang_v1.percentage'), 'selling_price_group' => __('lang_v1.selling_price_group')], $customer_group->price_calculation_type, ['class' => 'form-control'])); !!}
      </div>
      <div class="form-group percentage-field @if($customer_group->price_calculation_type != 'percentage') hide @endif">
        {!! html()->label(__( 'lang_v1.calculation_percentage' ) . ':', 'amount')->attributes([]) !!}
        @show_tooltip(__('lang_v1.tooltip_calculation_percentage'))
        {!! html()->text('amount', @num_format($customer_group->amount))->attributes(['class' => 'form-control input_number','placeholder' => __( 'lang_v1.calculation_percentage')]); !!}
      </div>

      <div class="form-group selling_price_group-field @if($customer_group->price_calculation_type != 'selling_price_group') hide @endif">
            {!! html()->label(__( 'lang_v1.selling_price_group' ) . ':', 'selling_price_group_id')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('selling_price_group_id', $price_groups, $customer_group->selling_price_group_id, ['class' => 'form-control'])); !!}
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->