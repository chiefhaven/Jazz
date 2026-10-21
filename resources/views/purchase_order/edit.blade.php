@extends('layouts.app')
@section('title', __('lang_v1.edit_purchase_order'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.edit_purchase_order') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">

  <!-- Page level currency setting -->
  <input type="hidden" id="p_code" value="{{$currency_details->code}}">
  <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
  <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
  <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

  @include('layouts.partials.error')

  {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' =>  action([\App\Http\Controllers\PurchaseOrderController::class, 'update'] , [$purchase->id] ), 'method' => 'PUT', 'id' => 'add_purchase_form', 'files' => true ]))->open() !!}

  @php
    $currency_precision = session('business.currency_precision', 2);
  @endphp
  <input type="hidden" id="is_purchase_order">
  <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">

    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
              <div class="form-group">
                {!! html()->label(__('purchase.supplier') . ':*', 'supplier_id')->attributes([]) !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('contact_id', [ $purchase->contact_id => $purchase->contact->name], $purchase->contact_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select') , 'required', 'id' => 'supplier_id'])); !!}
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                  </span>
                </div>
              </div>
              <strong>
                @lang('business.address'):
              </strong>
              <div id="supplier_address_div">
                {!! $purchase->contact->contact_address !!}
              </div>
            </div>

            <div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
              <div class="form-group">
                {!! html()->label(__('purchase.ref_no') . '*', 'ref_no')->attributes([]) !!}
                {!! html()->text('ref_no', $purchase->ref_no)->attributes(['class' => 'form-control', 'required']); !!}
              </div>
            </div>
            
            <div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
              <div class="form-group">
                {!! html()->label(__('lang_v1.order_date') . ':*', 'transaction_date')->attributes([]) !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!! html()->text('transaction_date', @format_datetime($purchase->transaction_date))->attributes(['class' => 'form-control', 'readonly', 'required']); !!}
                </div>
              </div>
            </div>
            <div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.delivery_date') . ':', 'delivery_date')->attributes([]) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! html()->text('delivery_date', $delivery_date)->attributes(['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                {!! html()->label(__('purchase.business_location').':*', 'location_id')->attributes([]) !!}
                @show_tooltip(__('tooltip.purchase_location'))
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $purchase->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'disabled'])); !!}
              </div>
            </div>

            <!-- Currency Exchange Rate -->
            <div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
              <div class="form-group">
                {!! html()->label(__('purchase.p_exchange_rate') . ':*', 'exchange_rate')->attributes([]) !!}
                @show_tooltip(__('tooltip.currency_exchange_factor'))
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                  </span>
                  {!! html()->input('number', 'exchange_rate', $purchase->exchange_rate)->attributes(['class' => 'form-control', 'required', 'step' => 0.001]); !!}
                </div>
                <span class="help-block text-danger">
                  @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                </span>
              </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                  <div class="multi-input">
                    {!! html()->label(__('contact.pay_term') . ':', 'pay_term_number')->attributes([]) !!} @show_tooltip(__('tooltip.pay_term'))
                    <br/>
                    {!! html()->input('number', 'pay_term_number', $purchase->pay_term_number)->attributes(['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('pay_term_type', ['months' => __('lang_v1.months'), 
                        'days' => __('lang_v1.days')], $purchase->pay_term_type, ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type'])); !!}
                  </div>
              </div>
          </div>

            <div class="col-sm-3">
                <div class="form-group">
                    {!! html()->label(__('purchase.attach_document') . ':', 'document')->attributes([]) !!}
                    {!! html()->file('document')->attributes(['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                    <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                    @includeIf('components.document_help_text')</p>
                </div>
            </div>
        </div>
        @if(!empty($common_settings['enable_purchase_requisition']))
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.purchase_requisition').':', 'purchase_requisition_ids')->attributes([]) !!}
                    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('purchase_requisition_ids[]', $purchase_requisitions, $purchase->purchase_requisition_ids, ['class' => 'form-control select2', 'multiple', 'id' => 'purchase_requisition_ids'])); !!}
                </div>
            </div>
        </div>
        @endif
    @endcomponent

    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-search"></i>
                  </span>
                  {!! html()->text('search_product', null)->attributes(['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
                </div>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action([\App\Http\Controllers\ProductController::class, 'quickAdd'])}}" 
                      data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
              </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
              @include('purchase.partials.edit_purchase_entry_row', ['is_purchase_order' => true])

              <hr/>
              <div class="pull-right col-md-5">
                <table class="pull-right col-md-12">
                  <tr>
                    <th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
                    <td class="col-md-5 text-left">
                      <span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
                    </td>
                  </tr>
                  <tr class="hide">
                    <th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
                    <td class="col-md-5 text-left">
                      <span id="total_st_before_tax" class="display_currency"></span>
                      <input type="hidden" id="st_before_tax_input" value=0>
                    </td>
                  </tr>
                  <tr>
                    <th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
                    <td class="col-md-5 text-left">
                      <span id="total_subtotal" class="display_currency">{{$purchase->total_before_tax/$purchase->exchange_rate}}</span>
                      <!-- This is total before purchase tax-->
                      <input type="hidden" id="total_subtotal_input" value="{{$purchase->total_before_tax/$purchase->exchange_rate}}" name="total_before_tax">
                    </td>
                  </tr>
                </table>
              </div>

            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-solid'])
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
                  {!! html()->label(__('sale.shipping_details'), 'shipping_details')->attributes([]) !!}
                  {!! html()->textarea('shipping_details', $purchase->shipping_details)->attributes(['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '3', 'cols'=>'30']); !!}
              </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
                  {!! html()->label(__('lang_v1.shipping_address'), 'shipping_address')->attributes([]) !!}
                  {!! html()->textarea('shipping_address', $purchase->shipping_address)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '3', 'cols'=>'30']); !!}
              </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!!html()->label(__('sale.shipping_charges'), 'shipping_charges')->attributes([])!!}
            <div class="input-group">
            <span class="input-group-addon">
            <i class="fa fa-info"></i>
            </span>
            {!!html()->text('shipping_charges', number_format($purchase->shipping_charges/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator))->attributes(['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4">
          <div class="form-group">
                  {!! html()->label(__('lang_v1.shipping_status'), 'shipping_status')->attributes([]) !!}
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('shipping_status', $shipping_statuses, $purchase->shipping_status, ['class' => 'form-control','placeholder' => __('messages.please_select')])); !!}
              </div>
        </div>
        <div class="col-md-4">
              <div class="form-group">
                  {!! html()->label(__('lang_v1.delivered_to') . ':', 'delivered_to')->attributes([]) !!}
                  {!! html()->text('delivered_to', $purchase->delivered_to)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.delivered_to')]); !!}
              </div>
          </div>
          @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
              $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

              $is_shipping_custom_field_1_required = !empty($custom_labels['shipping']['is_custom_field_1_required']) && $custom_labels['shipping']['is_custom_field_1_required'] == 1 ? true : false;

              $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

              $is_shipping_custom_field_2_required = !empty($custom_labels['shipping']['is_custom_field_2_required']) && $custom_labels['shipping']['is_custom_field_2_required'] == 1 ? true : false;

              $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';
              
              $is_shipping_custom_field_3_required = !empty($custom_labels['shipping']['is_custom_field_3_required']) && $custom_labels['shipping']['is_custom_field_3_required'] == 1 ? true : false;

              $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';
              
              $is_shipping_custom_field_4_required = !empty($custom_labels['shipping']['is_custom_field_4_required']) && $custom_labels['shipping']['is_custom_field_4_required'] == 1 ? true : false;

              $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
              
              $is_shipping_custom_field_5_required = !empty($custom_labels['shipping']['is_custom_field_5_required']) && $custom_labels['shipping']['is_custom_field_5_required'] == 1 ? true : false;
            @endphp

            @if(!empty($shipping_custom_label_1))
              @php
                $label_1 = $shipping_custom_label_1 . ':';
                if($is_shipping_custom_field_1_required) {
                  $label_1 .= '*';
                }
              @endphp

              <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_1, 'shipping_custom_field_1')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_1', $purchase->shipping_custom_field_1)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
                </div>
            </div>
            @endif
            @if(!empty($shipping_custom_label_2))
              @php
                $label_2 = $shipping_custom_label_2 . ':';
                if($is_shipping_custom_field_2_required) {
                  $label_2 .= '*';
                }
              @endphp

              <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_2, 'shipping_custom_field_2')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_2', $purchase->shipping_custom_field_2)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
                </div>
            </div>
            @endif
            @if(!empty($shipping_custom_label_3))
              @php
                $label_3 = $shipping_custom_label_3 . ':';
                if($is_shipping_custom_field_3_required) {
                  $label_3 .= '*';
                }
              @endphp

              <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_3, 'shipping_custom_field_3')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_3', $purchase->shipping_custom_field_3)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
                </div>
            </div>
            @endif
            @if(!empty($shipping_custom_label_4))
              @php
                $label_4 = $shipping_custom_label_4 . ':';
                if($is_shipping_custom_field_4_required) {
                  $label_4 .= '*';
                }
              @endphp

              <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_4, 'shipping_custom_field_4')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_4', $purchase->shipping_custom_field_4)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
                </div>
            </div>
            @endif
            @if(!empty($shipping_custom_label_5))
              @php
                $label_5 = $shipping_custom_label_5 . ':';
                if($is_shipping_custom_field_5_required) {
                  $label_5 .= '*';
                }
              @endphp

              <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label($label_5, 'shipping_custom_field_5')->attributes([]) !!}
                    {!! html()->text('shipping_custom_field_5', $purchase->shipping_custom_field_4)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
                </div>
            </div>
            @endif
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label(__('lang_v1.shipping_documents') . ':', 'shipping_documents')->attributes([]) !!}
                    {!! html()->file('shipping_documents[]')->attributes(['id' => 'shipping_documents', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                    <p class="help-block">
                      @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                      @includeIf('components.document_help_text')
                    </p>
                    @php
                        $medias = $purchase->media->where('model_media_type', 'shipping_document')->all();
                    @endphp
                    @include('sell.partials.media_table', ['medias' => $medias, 'delete' => true])
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 text-center">
              <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
            </div>
            <div class="col-md-8 col-md-offset-4" id="additional_expenses_div">
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th>@lang('lang_v1.additional_expense_name')</th>
                    <th>@lang('sale.amount')</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      {!! html()->text('additional_expense_key_1', $purchase->additional_expense_key_1)->attributes(['class' => 'form-control']); !!}
                    </td>
                    <td>
                      {!! html()->text('additional_expense_value_1', number_format($purchase->additional_expense_value_1/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator))->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {!! html()->text('additional_expense_key_2', $purchase->additional_expense_key_2)->attributes(['class' => 'form-control']); !!}
                    </td>
                    <td>
                      {!! html()->text('additional_expense_value_2', number_format($purchase->additional_expense_value_2/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator))->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {!! html()->text('additional_expense_key_3', $purchase->additional_expense_key_3)->attributes(['class' => 'form-control']); !!}
                    </td>
                    <td>
                      {!! html()->text('additional_expense_value_3', number_format($purchase->additional_expense_value_3/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator))->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {!! html()->text('additional_expense_key_4', $purchase->additional_expense_key_4)->attributes(['class' => 'form-control']); !!}
                    </td>
                    <td>
                      {!! html()->text('additional_expense_value_4', number_format($purchase->additional_expense_value_4/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator))->attributes(['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-8">
                {!! html()->hidden('final_total', $purchase->final_total)->attributes(['id' => 'grand_total_hidden']); !!}
                      <b>@lang('lang_v1.order_total'): </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>{{$purchase->final_total}}</span>
            </div>
          </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-solid'])
        <div class="row">
            <div class="col-sm-12">
                <table class="table">
                  <tr class="hide">
                    <td class="col-md-3">
                      <div class="form-group">
                        {!! html()->label(__( 'purchase.discount_type' ) . ':', 'discount_type')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], $purchase->discount_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
                      </div>
                    </td>
                    <td class="col-md-3">
                      <div class="form-group">
                      {!! html()->label(__( 'purchase.discount_amount' ) . ':', 'discount_amount')->attributes([]) !!}
                      {!! html()->text('discount_amount', ($purchase->discount_type == 'fixed' ? 
                        number_format($purchase->discount_amount/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)
                      :
                        number_format($purchase->discount_amount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)
                      ))->attributes(['class' => 'form-control input_number']); !!}
                      </div>
                    </td>
                    <td class="col-md-3">
                      &nbsp;
                    </td>
                    <td class="col-md-3">
                      <b>Discount:</b>(-) 
                      <span id="discount_calculated_amount" class="display_currency">0</span>
                    </td>
                  </tr>
                  <tr class="hide">
                    <td>
                      <div class="form-group">
                      {!! html()->label(__( 'purchase.purchase_tax' ) . ':', 'tax_id')->attributes([]) !!}
                      <select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
                        <option value="" data-tax_amount="0" selected>@lang('lang_v1.none')</option>
                        @foreach($taxes as $tax)
                          <option value="{{ $tax->id }}" @if($purchase->tax_id == $tax->id) {{'selected'}} @endif data-tax_amount="{{ $tax->amount }}"
                          >
                            {{ $tax->name }}
                          </option>
                        @endforeach
                      </select>
                      {!! html()->hidden('tax_amount', $purchase->tax_amount)->attributes(['id' => 'tax_amount']); !!}
                      </div>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>
                      <b>@lang( 'purchase.purchase_tax' ):</b>(+) 
                      <span id="tax_calculated_amount" class="display_currency">0</span>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="4">
                      <div class="form-group">
                        {!! html()->label(__('purchase.additional_notes'), 'additional_notes')->attributes([]) !!}
                        {!! html()->textarea('additional_notes', $purchase->additional_notes)->attributes(['class' => 'form-control', 'rows' => 3]); !!}
                      </div>
                    </td>
                  </tr>

                </table>
            </div>
        </div>
    @endcomponent
  
    <div class="row">
        <div class="col-sm-12 text-center">
          <button type="button" id="submit_purchase_form" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg">@lang('messages.update')</button>
        </div>
    </div>
{!! html()->form()->close() !!}
</section>
<!-- /.content -->
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  @include('contact.create', ['quick_add' => true])
</div>

@endsection

@section('javascript')
  <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
  <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
  <script type="text/javascript">
    $(document).ready( function(){
      update_table_total();
      update_grand_total();
      __page_leave_confirmation('#add_purchase_form');
        $('#shipping_documents').fileinput({
            showUpload: false,
            showPreview: false,
            browseLabel: LANG.file_browse_label,
            removeLabel: LANG.remove,
        });
    });
  </script>
  @include('purchase.partials.keyboard_shortcuts')
@endsection
