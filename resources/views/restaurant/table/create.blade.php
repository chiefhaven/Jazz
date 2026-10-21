<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\Restaurant\TableController::class, 'store']), 'method' => 'post', 'id' => 'table_add_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'restaurant.add_table' )</h4>
    </div>

    <div class="modal-body">

      @if(count($business_locations) == 1)
        @php 
            $default_location = current(array_keys($business_locations->toArray())) 
        @endphp
      @else
        @php $default_location = null; @endphp
      @endif
      <div class="form-group">
        {!! html()->label(__('purchase.business_location').':*', 'location_id')->attributes([]) !!}
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'])); !!}
      </div>
      
      <div class="form-group">
        {!! html()->label(__( 'restaurant.table_name' ) . ':*', 'name')->attributes([]) !!}
          {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'restaurant.table_name' ) ]); !!}
      </div>

      <div class="form-group">
        {!! html()->label(__( 'restaurant.short_description' ) . ':', 'description')->attributes([]) !!}
          {!! html()->text('description', null)->attributes(['class' => 'form-control','placeholder' => __( 'restaurant.short_description' )]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->