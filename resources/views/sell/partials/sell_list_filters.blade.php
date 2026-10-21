@if(empty($only) || in_array('sell_list_filter_location_id', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! html()->label(__('purchase.business_location') . ':', 'sell_list_filter_location_id')->attributes([]) !!}

        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ])); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sell_list_filter_customer_id', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! html()->label(__('contact.customer') . ':', 'sell_list_filter_customer_id')->attributes([]) !!}
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sell_list_filter_payment_status', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! html()->label(__('purchase.payment_status') . ':', 'sell_list_filter_payment_status')->attributes([]) !!}
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sell_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sell_list_filter_date_range', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! html()->label(__('report.date_range') . ':', 'sell_list_filter_date_range')->attributes([]) !!}
        {!! html()->text('sell_list_filter_date_range', null)->attributes(['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
    </div>
</div>
@endif
@if((empty($only) || in_array('created_by', $only)) && !empty($sales_representative))
<div class="col-md-3">
    <div class="form-group">
        {!! html()->label(__('report.user') . ':', 'created_by')->attributes([]) !!}
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%'])); !!}
    </div>
</div>
@endif
@if(empty($only) || in_array('sales_cmsn_agnt', $only))
@if(!empty($is_cmsn_agent_enabled))
    <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('lang_v1.sales_commission_agent') . ':', 'sales_cmsn_agnt')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sales_cmsn_agnt', $commission_agents, null, ['class' => 'form-control select2', 'style' => 'width:100%'])); !!}
        </div>
    </div>
@endif
@endif
@if(empty($only) || in_array('service_staffs', $only))
@if(!empty($service_staffs))
    <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('restaurant.service_staff') . ':', 'service_staffs')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('service_staffs', $service_staffs, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])); !!}
        </div>
    </div>
@endif
@endif
@if(!empty($shipping_statuses))
    <div class="col-md-3">
        <div class="form-group">
            {!! html()->label(__('lang_v1.shipping_status') . ':', 'shipping_status')->attributes([]) !!}
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('shipping_status', $shipping_statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')])); !!}
        </div>
    </div>
@endif
@if(empty($only) || in_array('only_subscriptions', $only))
<div class="col-md-3" style="margin-bottom: 7px;">
    <div class="form-group">
        <div class="checkbox">
            <label>
                <br>
              {!! html()->checkbox('only_subscriptions', false, 1)->attributes([ 'class' => 'input-icheck', 'id' => 'only_subscriptions']); !!} {{ __('lang_v1.subscriptions') }}
            </label>
        </div>
    </div>
</div>
@endif