<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\VariationTemplateController::class, 'update'], [$variation->id]), 'method' => 'PUT', 'id' => 'variation_edit_form', 'class' => 'form-horizontal' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.edit_variation')</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! html()->label(__('lang_v1.variation_name') . ':*', 'name')->attributes(['class' => 'col-sm-3 control-label']) !!}

        <div class="col-sm-9">
          {!! html()->text('name', $variation->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.variation_name')]); !!}
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">@lang('lang_v1.add_variation_values'):*</label>
        @foreach( $variation->values as $attr)
          @if( $loop->first )
            <div class="col-sm-7">
              {!! html()->text('edit_variation_values[' . $attr->id . ']', $attr->name)->attributes(['class' => 'form-control', 'required']); !!}
            </div>
          @endif
        @endforeach
        <div class="col-sm-2">
          <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="add_variation_values">+</button>
        </div>
      </div>
      <div id="variation_values">
        @foreach( $variation->values as $attr)
          @if( !$loop->first )
            <div class="form-group">
              <div class="col-sm-7 col-sm-offset-3">
                {!! html()->text('edit_variation_values[' . $attr->id . ']', $attr->name)->attributes(['class' => 'form-control', 'required']); !!}
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.update')</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->