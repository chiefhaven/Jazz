<!--Purchase related settings -->
<div class="pos-tab-content">
    <div class="row">
    @if(!config('constants.disable_purchase_in_other_currency', true))
    <div class="col-sm-4">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('purchase_in_diff_currency', $business->purchase_in_diff_currency, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'purchase_in_diff_currency']); !!} {{ __( 'purchase.allow_purchase_different_currency' ) }}
                </label>
              @show_tooltip(__('tooltip.purchase_different_currency'))
            </div>
        </div>
    </div>
    <div class="col-sm-4 @if($business->purchase_in_diff_currency != 1) hide @endif" id="settings_purchase_currency_div">
        <div class="form-group">
            {!! html()->label(__('purchase.purchase_currency') . ':', 'purchase_currency_id')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('purchase_currency_id', $currencies, $business->purchase_currency_id, ['class' => 'form-control select2', 'placeholder' => __('business.currency'), 'required', 'style' => 'width:100% !important'])); !!}
            </div>
        </div>
    </div>
    <div class="col-sm-4 @if($business->purchase_in_diff_currency != 1) hide @endif" id="settings_currency_exchange_div">
        <div class="form-group">
            {!! html()->label(__('purchase.p_exchange_rate') . ':', 'p_exchange_rate')->attributes([]) !!}
            @show_tooltip(__('tooltip.currency_exchange_factor'))
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! html()->input('number', 'p_exchange_rate', $business->p_exchange_rate)->attributes(['class' => 'form-control', 'placeholder' => __('business.p_exchange_rate'), 'required', 'step' => '0.001']); !!}
            </div>
        </div>
    </div>
    @endif
    <div class="clearfix"></div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="checkbox">
              <label>
                {!! html()->checkbox('enable_editing_product_from_purchase', $business->enable_editing_product_from_purchase, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_editing_product_from_purchase' ) }}
              </label>
              @show_tooltip(__('lang_v1.enable_updating_product_price_tooltip'))
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('enable_purchase_status', $business->enable_purchase_status, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'enable_purchase_status']); !!} {{ __( 'lang_v1.enable_purchase_status' ) }}
                </label>
              @show_tooltip(__('lang_v1.tooltip_enable_purchase_status'))
            </div>
        </div>
    </div>
<div class="clearfix"></div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('enable_lot_number', $business->enable_lot_number, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'enable_lot_number']); !!} {{ __( 'lang_v1.enable_lot_number' ) }}
                </label>
              @show_tooltip(__('lang_v1.tooltip_enable_lot_number'))
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('common_settings[enable_purchase_order]', !empty($common_settings['enable_purchase_order']), 1)->attributes([ 'class' => 'input-icheck', 'id' => 'enable_purchase_order']); !!} {{ __( 'lang_v1.enable_purchase_order' ) }}
                </label>
              @show_tooltip(__('lang_v1.purchase_order_help_text'))
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-6">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('common_settings[enable_purchase_requisition]', !empty($common_settings['enable_purchase_requisition']), 1)->attributes([ 'class' => 'input-icheck', 'id' => 'enable_purchase_requisition']); !!} {{ __( 'lang_v1.enable_purchase_requisition' ) }}
                </label>
              @show_tooltip(__('lang_v1.purchase_requisition_help_text'))
            </div>
        </div>
    </div>

    </div>
</div>