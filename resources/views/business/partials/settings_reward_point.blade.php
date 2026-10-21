<div class="pos-tab-content">
<div class="row well">
    <div class="col-sm-4">
        <div class="form-group">
            <div class="checkbox">
                <label>
                {!! html()->checkbox('enable_rp', $business->enable_rp, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'enable_rp']); !!} {{ __( 'lang_v1.enable_rp' ) }}
                </label>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.rp_name') . ':', 'rp_name')->attributes([]) !!}
            {!! html()->text('rp_name', $business->rp_name)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.rp_name')]); !!}
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="col-sm-12">
        <h4>@lang('lang_v1.earning_points_setting'):</h4>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.amount_for_unit_rp') . ':', 'amount_for_unit_rp')->attributes([]) !!} @show_tooltip(__('lang_v1.amount_for_unit_rp_tooltip'))
            {!! html()->text('amount_for_unit_rp', @num_format($business->amount_for_unit_rp))->attributes(['class' => 'form-control input_number','placeholder' => __('lang_v1.amount_for_unit_rp')]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.min_order_total_for_rp') . ':', 'min_order_total_for_rp')->attributes([]) !!} @show_tooltip(__('lang_v1.min_order_total_for_rp_tooltip'))
            {!! html()->text('min_order_total_for_rp', @num_format($business->min_order_total_for_rp))->attributes(['class' => 'form-control input_number','placeholder' => __('lang_v1.min_order_total_for_rp')]); !!}
        </div>
    </div>
    
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.max_rp_per_order') . ':', 'max_rp_per_order')->attributes([]) !!} @show_tooltip(__('lang_v1.max_rp_per_order_tooltip'))
            {!! html()->input('number', 'max_rp_per_order', $business->max_rp_per_order)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.max_rp_per_order')]); !!}
        </div>
    </div>
   </div>
   <div class="row well">
    <div class="col-sm-12">
        <h4>@lang('lang_v1.redeem_points_setting'):</h4>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.redeem_amount_per_unit_rp') . ':', 'redeem_amount_per_unit_rp')->attributes([]) !!} @show_tooltip(__('lang_v1.redeem_amount_per_unit_rp_tooltip'))
            {!! html()->text('redeem_amount_per_unit_rp', @num_format($business->redeem_amount_per_unit_rp))->attributes(['class' => 'form-control input_number','placeholder' => __('lang_v1.redeem_amount_per_unit_rp')]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.min_order_total_for_redeem') . ':', 'min_order_total_for_redeem')->attributes([]) !!} @show_tooltip(__('lang_v1.min_order_total_for_redeem_tooltip'))
            {!! html()->text('min_order_total_for_redeem', @num_format($business->min_order_total_for_redeem))->attributes(['class' => 'form-control input_number','placeholder' => __('lang_v1.min_order_total_for_redeem')]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.min_redeem_point') . ':', 'min_redeem_point')->attributes([]) !!} @show_tooltip(__('lang_v1.min_redeem_point_tooltip'))
            {!! html()->input('number', 'min_redeem_point', $business->min_redeem_point)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.min_redeem_point')]); !!}
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! html()->label(__('lang_v1.max_redeem_point') . ':', 'max_redeem_point')->attributes([]) !!} @show_tooltip(__('lang_v1.max_redeem_point_tooltip'))
            {!! html()->input('number', 'max_redeem_point', $business->max_redeem_point)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.max_redeem_point')]); !!}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            {!! html()->label(__('lang_v1.rp_expiry_period') . ':', 'rp_expiry_period')->attributes([]) !!} @show_tooltip(__('lang_v1.rp_expiry_period_tooltip'))
            <div class="input-group">
                {!! html()->input('number', 'rp_expiry_period', $business->rp_expiry_period)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.rp_expiry_period')]); !!}
                <span class="input-group-addon">-</span>
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('rp_expiry_type', ['month' => __('lang_v1.month'), 'year' => __('lang_v1.year')], $business->rp_expiry_type, ['class' => 'form-control'])); !!}
            </div>
        </div>
    </div>
    </div>
</div>