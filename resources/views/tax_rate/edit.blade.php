<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\TaxRateController::class, 'update'], [$tax_rate->id]), 'method' => 'PUT', 'id' => 'tax_rate_edit_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tax_rate.edit_taxt_rate' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! html()->label(__( 'tax_rate.name' ) . ':*', 'name')->attributes([]) !!}
          {!! html()->text('name', $tax_rate->name)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'tax_rate.name' )]); !!}
      </div>

      <div class="form-group">
        {!! html()->label(__( 'tax_rate.rate' ) . ':*', 'amount')->attributes([]) !!} @show_tooltip(__('lang_v1.tax_exempt_help'))
          {!! html()->text('amount', $tax_rate->amount)->attributes(['class' => 'form-control input_number', 'required']); !!}
      </div>

      <div class="form-group">
        <div class="checkbox">
          <label>
             {!! html()->checkbox('for_tax_group', !empty($tax_rate->for_tax_group), 1)->attributes([ 'class' => 'input_icheck']); !!} @lang( 'lang_v1.for_tax_group_only' )
          </label> @show_tooltip(__('lang_v1.for_tax_group_only_help'))
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