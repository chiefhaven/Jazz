<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\AccountController::class, 'postFundTransfer']), 'method' => 'post', 'id' => 'fund_transfer_form', 'files' => true ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.fund_transfer' )</h4>
    </div>

    <div class="modal-body">

            <div class="form-group">
                {!! html()->label(__( 'lang_v1.transfer_from' ) .":*", 'from_account')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('from_account', $to_accounts, $from_account->id, ['class' => 'form-control', 'required' ])); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__( 'account.transfer_to' ) .":*", 'to_account')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('to_account', $to_accounts, null, ['class' => 'form-control', 'required' ])); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__( 'sale.amount' ) .":*", 'amount')->attributes([]) !!}
                {!! html()->text('amount', 0)->attributes(['class' => 'form-control input_number', 'required','placeholder' => __( 'sale.amount' ) ]); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__( 'messages.date' ) .":*", 'od_datetimepicker')->attributes([]) !!}
                <div class="input-group">
                  {!! html()->text('operation_date', null)->attributes(['class' => 'form-control', 'required','placeholder' => __( 'messages.date' ), 'id' => 'od_datetimepicker' ]); !!}
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
            </div>

            <div class="form-group">
                {!! html()->label(__( 'brand.note' ), 'note')->attributes([]) !!}
                {!! html()->textarea('note', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>

            <div class="form-group">
                {!! html()->label(__('purchase.attach_document') . ':', 'document')->attributes([]) !!}
                {!! html()->file('document')->attributes(['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                <p class="help-block">
                  @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                  @includeIf('components.document_help_text')
                </p>
            </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.submit' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! html()->form()->close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready( function(){
    $('#od_datetimepicker').datetimepicker({
      format: moment_date_format + ' ' + moment_time_format
    });
  });
</script>