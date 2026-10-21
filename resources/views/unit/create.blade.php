<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\UnitController::class, 'store']), 'method' => 'post', 'id' => $quick_add ? 'quick_add_unit_form' : 'unit_add_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'unit.add_unit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! html()->label(__( 'unit.name' ) . ':*', 'actual_name')->attributes([]) !!}
            {!! html()->text('actual_name', null)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}
        </div>

        <div class="form-group col-sm-12">
          {!! html()->label(__( 'unit.short_name' ) . ':*', 'short_name')->attributes([]) !!}
            {!! html()->text('short_name', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'unit.short_name' ), 'required']); !!}
        </div>

        <div class="form-group col-sm-12">
          {!! html()->label(__( 'unit.allow_decimal' ) . ':*', 'allow_decimal')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('allow_decimal', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder' => __( 'messages.please_select' ), 'required', 'class' => 'form-control'])); !!}
        </div>
        @if(!$quick_add)
          <div class="form-group col-sm-12">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                     {!! html()->checkbox('define_base_unit', false, 1)->attributes([ 'class' => 'toggler', 'data-toggle_id' => 'base_unit_div' ]); !!} @lang( 'lang_v1.add_as_multiple_of_base_unit' )
                  </label> @show_tooltip(__('lang_v1.multi_unit_help'))
                </div>
            </div>
          </div>
          <div class="form-group col-sm-12 hide" id="base_unit_div">
            <table class="table">
              <tr>
                <th style="vertical-align: middle;">1 <span id="unit_name">@lang('product.unit')</span></th>
                <th style="vertical-align: middle;">=</th>
                <td style="vertical-align: middle;">
                  {!! html()->text('base_unit_multiplier', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.times_base_unit' )]); !!}</td>
                <td style="vertical-align: middle;">
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('base_unit_id', $units, null, ['placeholder' => __( 'lang_v1.select_base_unit' ), 'class' => 'form-control'])); !!}
                </td>
              </tr>
            </table>
          </div>
        @endif
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->