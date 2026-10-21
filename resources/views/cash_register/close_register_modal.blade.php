<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\CashRegisterController::class, 'postCloseRegister']), 'method' => 'post' ]))->open() !!}

    {!! html()->hidden('user_id', $register_details->user_id)->attributes([]); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">@lang( 'cash_register.current_register' ) ( {{ \Carbon::createFromFormat('Y-m-d H:i:s', $register_details->open_time)->format('jS M, Y h:i A') }} - {{ \Carbon::now()->format('jS M, Y h:i A') }})</h3>
    </div>

    <div class="modal-body">
      @if(auth()->user()->can('view_cash_register'))
        @include('cash_register.payment_details')
        <hr>
      @endif
      <div class="row">
      @if(auth()->user()->can('view_cash_register'))
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__( 'cash_register.total_cash' ) . ':*', 'closing_amount')->attributes([]) !!}
              {!! html()->text('closing_amount', @num_format($register_details->cash_in_hand + $register_details->total_cash - $register_details->total_cash_refund - $register_details->total_cash_expense))->attributes(['class' => 'form-control input_number', 'required', 'placeholder' => __( 'cash_register.total_cash' ) ]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__( 'cash_register.total_card_slips' ) . ':*', 'total_card_slips')->attributes([]) !!} @show_tooltip(__('tooltip.total_card_slips'))
              {!! html()->input('number', 'total_card_slips', $register_details->total_card_slips)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'cash_register.total_card_slips' ), 'min' => 0 ]); !!}
          </div>
        </div> 
        <div class="col-sm-4">
          <div class="form-group">
            {!! html()->label(__( 'cash_register.total_cheques' ) . ':*', 'total_cheques')->attributes([]) !!} @show_tooltip(__('tooltip.total_cheques'))
              {!! html()->input('number', 'total_cheques', $register_details->total_cheques)->attributes(['class' => 'form-control', 'required', 'placeholder' => __( 'cash_register.total_cheques' ), 'min' => 0 ]); !!}
          </div>
        </div> 
        <hr>
        <div class="col-md-8 col-sm-12">
          <h3>@lang( 'lang_v1.cash_denominations' )</h3>
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
                  <td><span class="denomination_total">0</span></td>
                </tr>
              </tfoot>
            </table>
          @else
            <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
          @endif
        </div>
      @endif
        <hr>

        <div class="col-sm-12">
          <div class="form-group">
            {!! html()->label(__( 'cash_register.closing_note' ) . ':', 'closing_note')->attributes([]) !!}
              {!! html()->textarea('closing_note', null)->attributes(['class' => 'form-control', 'placeholder' => __( 'cash_register.closing_note' ), 'rows' => 3 ]); !!}
          </div>
        </div>
      </div> 

      <div class="row">
        <div class="col-xs-6">
          <b>@lang('report.user'):</b> {{ $register_details->user_name}}<br>
          <b>@lang('business.email'):</b> {{ $register_details->email}}<br>
          <b>@lang('business.business_location'):</b> {{ $register_details->location_name}}<br>
        </div>
        @if(!empty($register_details->closing_note))
          <div class="col-xs-6">
            <strong>@lang('cash_register.closing_note'):</strong><br>
            {{$register_details->closing_note}}
          </div>
        @endif
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.cancel' )</button>
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'cash_register.close_register' )</button>
    </div>
    {!! html()->form()->close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->