<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\WarrantyController::class, 'update'], [$warranty->id]), 'method' => 'put', 'id' => 'warranty_form']))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_warranty' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! html()->label(__( 'lang_v1.name' ) . ':*', 'name')->attributes([]) !!}
          {!! html()->text('name', $warranty->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ) ]); !!}
      </div>

      <div class="form-group">
        {!! html()->label(__( 'lang_v1.description' ) . ':', 'description')->attributes([]) !!}
          {!! html()->textarea('description', $warranty->description)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.description' ), 'rows' => 3 ]); !!}
      </div>
      <strong>{!! html()->label(__( 'lang_v1.duration' ) . ':', 'duration')->attributes([]) !!}*</strong>
      <div class="form-group">
          {!! html()->input('number', 'duration', $warranty->duration)->attributes(['class' => 'form-control width-40 pull-left', 'placeholder' => __( 'lang_v1.duration' ), 'required' ]); !!}

          {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('duration_type', ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')], $warranty->duration_type, ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'required'])); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->