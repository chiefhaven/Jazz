<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('woocommerce::lang.woocommerce_app_url') . ':', 'woocommerce_app_url')->attributes([]) !!}
            	{!! html()->text('woocommerce_app_url', $default_settings['woocommerce_app_url'])->attributes(['class' => 'form-control','placeholder' => __('woocommerce::lang.woocommerce_app_url')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('woocommerce::lang.woocommerce_consumer_key') . ':', 'woocommerce_consumer_key')->attributes([]) !!}
                {!! html()->text('woocommerce_consumer_key', $default_settings['woocommerce_consumer_key'])->attributes(['class' => 'form-control','placeholder' => __('woocommerce::lang.woocommerce_consumer_key')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('woocommerce::lang.woocommerce_consumer_secret') . ':', 'woocommerce_consumer_secret')->attributes([]) !!}
                <input type="password" name="woocommerce_consumer_secret" value="{{$default_settings['woocommerce_consumer_secret']}}" id="woocommerce_consumer_secret" class="form-control">
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('business.business_locations') . ':', 'location_id')->attributes([]) !!} @show_tooltip(__('woocommerce::lang.location_dropdown_help'))
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $locations, $default_settings['location_id'], ['class' => 'form-control'])); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="checkbox">
                <label>
                    <br/>
                    {!! html()->checkbox('enable_auto_sync', !empty($default_settings['enable_auto_sync']), 1)->attributes(['class' => 'input-icheck']); !!} @lang('woocommerce::lang.enable_auto_sync')
                </label>
                @show_tooltip(__('woocommerce::lang.auto_sync_tooltip'))
            </div>
        </div>
    </div>
</div>