<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label>
                {!! html()->checkbox('enable_offline_payment', !empty($settings["enable_offline_payment"]), 1)->attributes([ 'class' => 'input-icheck']); !!}
                @lang('superadmin::lang.enable_offline_payment')
                </label>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.offline_payment_details') . ':', 'offline_payment_details')->attributes([]) !!}
                @show_tooltip(__('superadmin::lang.offline_payment_details_tooltip'))
                {!! html()->textarea('offline_payment_details', !empty($settings["offline_payment_details"]) ? $settings["offline_payment_details"] : null)->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.offline_payment_details'), 'rows' => 3]); !!}
            </div>
        </div>
    </div>
    <div class="row">
    	<h4>Stripe:</h4>
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.stripe_pub_key') . ':', 'STRIPE_PUB_KEY')->attributes([]) !!}
            	{!! html()->text('STRIPE_PUB_KEY', $default_values['STRIPE_PUB_KEY'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.stripe_pub_key')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.stripe_secret_key') . ':', 'STRIPE_SECRET_KEY')->attributes([]) !!}
            	{!! html()->text('STRIPE_SECRET_KEY', $default_values['STRIPE_SECRET_KEY'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.stripe_secret_key')]); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        
        <h4>Paypal:</h4>
        <div class="col-xs-6">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.paypal_mode') . ':', 'PAYPAL_MODE')->attributes([]) !!}
            	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('PAYPAL_MODE', ['live' => 'Live', 'sandbox' => 'Sandbox'], $default_values['PAYPAL_MODE'], ['class' => 'form-control','placeholder' => __('messages.please_select')])); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.paypal_sandbox_api_username') . ':', 'PAYPAL_SANDBOX_API_USERNAME')->attributes([]) !!}
            	{!! html()->text('PAYPAL_SANDBOX_API_USERNAME', $default_values['PAYPAL_SANDBOX_API_USERNAME'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.paypal_sandbox_api_password') . ':', 'PAYPAL_SANDBOX_API_PASSWORD')->attributes([]) !!}
            	{!! html()->text('PAYPAL_SANDBOX_API_PASSWORD', $default_values['PAYPAL_SANDBOX_API_PASSWORD'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.paypal_sandbox_api_secret') . ':', 'PAYPAL_SANDBOX_API_SECRET')->attributes([]) !!}
            	{!! html()->text('PAYPAL_SANDBOX_API_SECRET', $default_values['PAYPAL_SANDBOX_API_SECRET'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_secret')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.paypal_live_api_username') . ':', 'PAYPAL_LIVE_API_USERNAME')->attributes([]) !!}
            	{!! html()->text('PAYPAL_LIVE_API_USERNAME', $default_values['PAYPAL_LIVE_API_USERNAME'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.paypal_live_api_password') . ':', 'PAYPAL_LIVE_API_PASSWORD')->attributes([]) !!}
            	{!! html()->text('PAYPAL_LIVE_API_PASSWORD', $default_values['PAYPAL_LIVE_API_PASSWORD'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.paypal_live_api_secret') . ':', 'PAYPAL_LIVE_API_SECRET')->attributes([]) !!}
            	{!! html()->text('PAYPAL_LIVE_API_SECRET', $default_values['PAYPAL_LIVE_API_SECRET'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_secret')]); !!}
            </div>
        </div>

        
        <div class="clearfix"></div>
        
        <h4>ONEKHUSA:</h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label('Public key:', 'ONEKHUSA_BASEURL')->attributes([]) !!}
                {!! html()->text('ONEKHUSA_BASEURL', $default_values['ONEKHUSA_BASEURL'])->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label('Merchant Account Number:', 'ONEKHUSA_MERCHANT_ACCOUNT_NUMBER')->attributes([]) !!}
                {!! html()->text('ONEKHUSA_MERCHANT_ACCOUNT_NUMBER', $default_values['ONEKHUSA_MERCHANT_ACCOUNT_NUMBER'])->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label('API Secret:', 'ONEKHUSA_API_SECRET')->attributes([]) !!}
                {!! html()->text('ONEKHUSA_API_SECRET', $default_values['ONEKHUSA_API_SECRET'])->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label('API Key:', 'ONEKHUSA_API_KEY')->attributes([]) !!}
                {!! html()->text('ONEKHUSA_API_KEY', $default_values['ONEKHUSA_API_KEY'])->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label('Organisation ID:', 'ONEKHUSA_ORGANISATION_ID')->attributes([]) !!}
                {!! html()->text('ONEKHUSA_ORGANISATION_ID', $default_values['ONEKHUSA_ORGANISATION_ID'])->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <br/>
            <p class="help-block"><i>@lang('superadmin::lang.payment_gateway_help')</i></p>
        </div>
    </div>
</div>