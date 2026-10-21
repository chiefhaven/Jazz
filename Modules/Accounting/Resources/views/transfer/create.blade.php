<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action('\Modules\Accounting\Http\Controllers\TransferController@store'), 
        'method' => 'post', 'id' => 'transfer_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'accounting::lang.add_transfer' )</h4>
    </div>

    <div class="modal-body">
        <div class="form-group">
            {!! html()->label(__('purchase.ref_no').':', 'ref_no')->attributes([]) !!}
            @show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
            {!! html()->text('ref_no', null)->attributes(['class' => 'form-control']); !!}
        </div>
        <div class="form-group">
            {!! html()->label(__( 'lang_v1.transfer_from' ) .":*", 'from_account')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('from_account', [], null, ['class' => 'form-control accounts-dropdown', 'required', 
                'placeholder' => __('messages.please_select') ])); !!}
        </div>

        <div class="form-group">
            {!! html()->label(__( 'account.transfer_to' ) .":*", 'to_account')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('to_account', [], null, ['class' => 'form-control accounts-dropdown', 'required', 
                'placeholder' => __('messages.please_select') ])); !!}
        </div>

        <div class="form-group">
            {!! html()->label(__( 'sale.amount' ) .":*", 'amount')->attributes([]) !!}
            {!! html()->text('amount', 0)->attributes(['class' => 'form-control input_number', 
                'required','placeholder' => __( 'sale.amount' ) ]); !!}
        </div>

        <div class="form-group">
            {!! html()->label(__( 'messages.date' ) .":*", 'operation_date')->attributes([]) !!}
            <div class="input-group">
                {!! html()->text('operation_date', null)->attributes(['class' => 'form-control', 
                    'required','placeholder' => __( 'messages.date' ), 'id' => 'operation_date' ]); !!}
                <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__( 'brand.note' ), 'note')->attributes([]) !!}
            {!! html()->textarea('note', null)->attributes(['class' => 'form-control', 
                'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
        </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->