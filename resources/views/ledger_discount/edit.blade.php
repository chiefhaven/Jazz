<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\LedgerDiscountController::class, 'update'], $discount->id), 'method' => 'put', 'id' => 'edit_discount_form' ]))->open() !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.edit_discount')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! html()->label(__( 'lang_v1.date' ) . ':*', 'edit_discount_date')->attributes([]) !!}
                  {!! html()->text('date', @format_datetime($discount->transaction_date))->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.date' ), 'id' => 'edit_discount_date']); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__( 'sale.amount' ) . ':*', 'amount')->attributes([]) !!}
                  {!! html()->text('amount', @num_format($discount->final_total))->attributes(['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.amount' ) ]); !!}
            </div>

            @if($contact->type == 'both')
            <div class="form-group">
                {!! html()->label(__( 'lang_v1.discount_for' ) . ':', 'sub_type')->attributes([]) !!}
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sub_type', ['sell_discount' => __('sale.sale'), 'purchase_discount' => __('lang_v1.purchase')], $discount->sub_type, ['class' => 'form-control', 'required' ])); !!}
            </div>
            @endif

            <div class="form-group">
                {!! html()->label(__( 'brand.note' ) . ':', 'note')->attributes([]) !!}
                  {!! html()->textarea('note', $discount->additional_notes)->attributes(['class' => 'form-control', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! html()->form()->close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->   