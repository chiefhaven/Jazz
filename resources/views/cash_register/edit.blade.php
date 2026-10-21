<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\BusinessLocationController::class, 'update'], [$location->id]), 'method' => 'PUT', 'id' => 'business_location_add_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'business.edit_business_location' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__( 'invoice.name' ) . ':*', 'name')->attributes([]) !!}
              {!! html()->text('name', $location->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'invoice.name' ) ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__( 'business.landmark' ) . ':', 'landmark')->attributes([]) !!}
              {!! html()->text('landmark', $location->landmark)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.landmark' ) ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__( 'business.city' ) . ':*', 'city')->attributes([]) !!}
              {!! html()->text('city', $location->city)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.city'), 'required' ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__( 'business.zip_code' ) . ':*', 'zip_code')->attributes([]) !!}
              {!! html()->text('zip_code', $location->zip_code)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.zip_code'), 'required' ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__( 'business.state' ) . ':*', 'state')->attributes([]) !!}
              {!! html()->text('state', $location->state)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.state'), 'required' ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__( 'business.country' ) . ':*', 'country')->attributes([]) !!}
              {!! html()->text('country', $location->country)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.country'), 'required' ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__('invoice.invoice_scheme') . ':*', 'invoice_scheme_id')->attributes([]) !!} @show_tooltip(__('tooltip.invoice_scheme'))
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('invoice_scheme_id', $invoice_schemes, $location->invoice_scheme_id, ['class' => 'form-control', 'required',
              'placeholder' => __('messages.please_select')])); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__('invoice.invoice_layout') . ':*', 'invoice_layout_id')->attributes([]) !!} @show_tooltip(__('tooltip.invoice_layout'))
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('invoice_layout_id', $invoice_layouts, $location->invoice_layout_id, ['class' => 'form-control', 'required',
              'placeholder' => __('messages.please_select')])); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->