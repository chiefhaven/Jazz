<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\TypesOfServiceController::class, 'update'], $type_of_service->id), 'method' => 'put', 'id' => 'types_of_service_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_type_of_service' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
      <div class="form-group col-md-12">
        {!! html()->label(__( 'tax_rate.name' ) . ':*', 'name')->attributes([]) !!}
          {!! html()->text('name', $type_of_service->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'tax_rate.name' )]); !!}
      </div>

      <div class="form-group col-md-12">
        {!! html()->label(__( 'lang_v1.description' ) . ':', 'description')->attributes([]) !!}
          {!! html()->textarea('description', $type_of_service->description)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.description' ), 'rows' => 3]); !!}
      </div>
      <div class="form-group col-md-12">
      <table class="table table-slim">
        <thead>
          <tr>
            <th>@lang('sale.location')</th>
            <th>@lang('lang_v1.price_group')</th> 
          </tr>
          @foreach($locations as $key => $value)
            <tr>
              <td>{{$value}}</td>
              <td>{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_price_group[' . $key . ']', $price_groups, !empty($type_of_service->location_price_group[$key]) ? $type_of_service->location_price_group[$key] : null, ['class' => 'form-control input-sm select2', 'style' => 'width: 100%;'])); !!}</td>
            </tr>
          @endforeach
        </thead>
      </table>
      </div>
       <div class="form-group col-md-6">
        {!! html()->label(__( 'lang_v1.packing_charge_type' ) . ':', 'packing_charge_type')->attributes([]) !!}
          {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('packing_charge_type', ['fixed' => __('lang_v1.fixed'), 'percent' => __('lang_v1.percentage')], $type_of_service->packing_charge_type, ['class' => 'form-control'])); !!}
      </div>
      <div class="form-group col-md-6">
        {!! html()->label(__( 'lang_v1.packing_charge' ) . ':', 'packing_charge')->attributes([]) !!}
          {!! html()->text('packing_charge', !empty($type_of_service->packing_charge) ? @num_format($type_of_service->packing_charge) : '')->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.packing_charge' )]); !!}
      </div>
      <div class="form-group col-md-12">
          <div class="checkbox">
            <label>
               {!! html()->checkbox('enable_custom_fields', !empty($type_of_service->enable_custom_fields), 1)->attributes([]); !!} @lang( 'lang_v1.enable_custom_fields' )
            </label> @show_tooltip(__('lang_v1.types_of_service_custom_field_help'))
          </div>
      </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->