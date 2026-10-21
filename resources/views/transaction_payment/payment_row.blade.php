<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\TransactionPaymentController::class, 'store']), 'method' => 'post', 'id' => 'transaction_payment_add_form', 'files' => true ]))->open() !!}
    {!! html()->hidden('transaction_id', $transaction->id)->attributes([]); !!}
    @if(!empty($transaction->location))
      {!! html()->hidden('default_payment_accounts', $transaction->location->default_payment_accounts)->attributes(['id' => 'default_payment_accounts']); !!}
    @endif
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'purchase.add_payment' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
      @if(!empty($transaction->contact))
        <div class="col-md-4">
          <div class="well">
            <strong>
            @if(in_array($transaction->type, ['purchase', 'purchase_return']))
              @lang('purchase.supplier') 
            @elseif(in_array($transaction->type, ['sell', 'sell_return']))
              @lang('contact.customer') 
            @endif
            </strong>:{{ $transaction->contact->full_name_with_business }}<br>
            <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
          </div>
        </div>
        @endif
        <div class="col-md-4">
          <div class="well">
          @if(in_array($transaction->type, ['sell', 'sell_return']))
            <strong>@lang('sale.invoice_no'): </strong>{{ $transaction->invoice_no }}
          @else
            <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
          @endif
          @if(!empty($transaction->location))
            <br>
            <strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
          @endif
          </div>
        </div>
        <div class="col-md-4">
          <div class="well">
            <strong>@lang('sale.total_amount'): </strong><span class="display_currency" data-currency_symbol="true">{{ $transaction->final_total }}</span><br>
            <strong>@lang('purchase.payment_note'): </strong>
            @if(!empty($transaction->additional_notes))
            {{ $transaction->additional_notes }}
            @else
              --
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          @if(!empty($transaction->contact))
            <strong>@lang('lang_v1.advance_balance'):</strong> <span class="display_currency" data-currency_symbol="true">{{$transaction->contact->balance}}</span>

            {!! html()->hidden('advance_balance', $transaction->contact->balance)->attributes(['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
          @endif
        </div>
      </div>
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! html()->label(__('purchase.payment_method') . ':*', "method")->attributes([]) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;'])); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! html()->label(__('lang_v1.paid_on') . ':*', "paid_on")->attributes([]) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! html()->text('paid_on', @format_datetime($payment_line->paid_on))->attributes(['class' => 'form-control', 'readonly', 'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! html()->label(__('sale.amount') . ':*', "amount")->attributes([]) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              {!! html()->text("amount", @num_format($payment_line->amount))->attributes(['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => 'Amount', 'data-rule-max-value' => $payment_line->amount, 'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])]); !!}
            </div>
          </div>
        </div>

        @php
            $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];
            $enable_cash_denomination_for_payment_methods = !empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
        @endphp

        @if(!empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens')
            <input type="hidden" class="enable_cash_denomination_for_payment_methods" value="{{json_encode($pos_settings['enable_cash_denomination_for_payment_methods'])}}">
            <div class="clearfix"></div>
            <div class="col-md-12 cash_denomination_div @if(!in_array($payment_line->method, $enable_cash_denomination_for_payment_methods)) hide @endif">
                <hr>
                <strong>@lang( 'lang_v1.cash_denominations' )</strong>
                  @if(!empty($pos_settings['cash_denominations']))
                    <table class="table table-slim">
                      <thead>
                        <tr>
                          <th width="20%" class="text-right">@lang('lang_v1.denomination')</th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-center">@lang('lang_v1.count')</th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-left">@lang('sale.subtotal')</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach(explode(',', $pos_settings['cash_denominations']) as $dnm)
                        <tr>
                          <td class="text-right">{{$dnm}}</td>
                          <td class="text-center" >X</td>
                          <td>{!! html()->input('number', "denominations[$dnm]", null)->attributes(['class' => 'form-control cash_denomination input-sm', 'min' => 0, 'data-denomination' => $dnm, 'style' => 'width: 100px; margin:auto;' ]); !!}</td>
                          <td class="text-center">=</td>
                          <td class="text-left">
                            <span class="denomination_subtotal">0</span>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="4" class="text-center">@lang('sale.total')</th>
                          <td>
                            <span class="denomination_total">0</span>
                            <input type="hidden" class="denomination_total_amount" value="0">
                            <input type="hidden" class="is_strict" value="{{$pos_settings['cash_denomination_strict_check'] ?? ''}}">
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                    <p class="cash_denomination_error error hide">@lang('lang_v1.cash_denomination_error')</p>
                  @else
                    <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
                  @endif
            </div>
            <div class="clearfix"></div>
        @endif
        @if(!empty($accounts))
          <div class="col-md-6">
            <div class="form-group">
              {!! html()->label(__('lang_v1.payment_account') . ':', "account_id")->attributes([]) !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '', ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;'])); !!}
              </div>
            </div>
          </div>
        @endif
        <div class="col-md-4">
          <div class="form-group">
            {!! html()->label(__('purchase.attach_document') . ':', 'document')->attributes([]) !!}
            {!! html()->file('document')->attributes(['accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
            <p class="help-block">
            @includeIf('components.document_help_text')</p>
          </div>
        </div>
        <div class="clearfix"></div>
          @include('transaction_payment.payment_type_details')
        <div class="col-md-12">
          <div class="form-group">
            {!! html()->label(__('lang_v1.payment_note') . ':', "note")->attributes([]) !!}
            {!! html()->textarea("note", $payment_line->note)->attributes(['class' => 'form-control', 'rows' => 3]); !!}
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