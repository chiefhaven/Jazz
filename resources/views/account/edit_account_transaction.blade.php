<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\AccountController::class, 'updateAccountTransaction'], ['id' => $account_transaction->id ]), 'method' => 'post', 'id' => 'edit_account_transaction_form' ]))->open() !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@if($account_transaction->sub_type == 'opening_balance')@lang( 'lang_v1.edit_opening_balance' ) @elseif($account_transaction->sub_type == 'fund_transfer') @lang( 'lang_v1.edit_fund_transfer' ) @elseif($account_transaction->sub_type == 'deposit') @lang( 'lang_v1.edit_deposit' ) @endif</h4>
    </div>

    <div class="modal-body">
            <div class="form-group">
                <strong>@lang('account.selected_account')</strong>: 
                {{$account_transaction->account->name}}
            </div>

            @if($account_transaction->sub_type == 'deposit')
            @php
              $label = !empty($account_transaction->type == 'debit') ? __( 'account.deposit_from' ) :  __('lang_v1.deposit_to');
            @endphp 
            <div class="form-group">  
                {!! html()->label($label .":", 'account_id')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('account_id', $accounts, $account_transaction->account_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select') ])); !!}
            </div>
            @endif

            @if($account_transaction->sub_type == 'fund_transfer') 
            @php
              $label = !empty($account_transaction->type == 'credit') ? __( 'account.transfer_to' ) :__('lang_v1.transfer_from')  ;
            @endphp 
            <div class="form-group">  
                {!! html()->label($label .":", 'account_id')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('account_id', $accounts, $account_transaction->account_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select') ])); !!}
            </div>
            @endif

            <div class="form-group">
                {!! html()->label(__( 'sale.amount' ) .":*", 'amount')->attributes([]) !!}
                {!! html()->text('amount', @num_format($account_transaction->amount))->attributes(['class' => 'form-control input_number', 'required','placeholder' => __( 'sale.amount' ) ]); !!}
            </div>
            @if($account_transaction->sub_type == 'deposit')
            @php
              $label = !empty($account_transaction->type == 'debit') ? __('lang_v1.deposit_to') :  __( 'account.deposit_from' );
            @endphp 
            <div class="form-group">  
                {!! html()->label($label .":", 'from_account')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('from_account', $accounts, $account_transaction->transfer_transaction->account_id ?? null, ['class' => 'form-control', 'placeholder' => __('messages.please_select') ])); !!}
            </div>
            @endif
            @if($account_transaction->sub_type == 'fund_transfer') 
            @php
              $label = !empty($account_transaction->type == 'credit') ? __('lang_v1.transfer_from') :  __( 'account.transfer_to' );
            @endphp 
            <div class="form-group">
                {!! html()->label($label .":*", 'to_account')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('to_account', $accounts, $account_transaction->transfer_transaction->account_id ?? null, ['class' => 'form-control', 'required' ])); !!}
            </div>
            @endif
 
            <div class="form-group">
                {!! html()->label(__( 'messages.date' ) .":*", 'operation_date')->attributes([]) !!}
                <div class="input-group date">
                  {!! html()->text('operation_date', @format_datetime($account_transaction->operation_date))->attributes(['class' => 'form-control', 'required','placeholder' => __( 'messages.date' ), 'id' => 'od_datetimepicker' ]); !!}
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
            </div>
            @if($account_transaction->sub_type == 'fund_transfer' || $account_transaction->sub_type == 'deposit')
            <div class="form-group">
                {!! html()->label(__( 'brand.note' ), 'note')->attributes([]) !!}
                {!! html()->textarea('note', $account_transaction->note)->attributes(['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>
            @endif
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