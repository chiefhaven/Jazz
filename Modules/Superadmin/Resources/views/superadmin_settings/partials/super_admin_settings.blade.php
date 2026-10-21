<div class="pos-tab-content active">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('business.business_name') . ':', 'invoice_business_name')->attributes([]) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-suitcase"></i>
                    </span>
                {!! html()->text('invoice_business_name', $settings["invoice_business_name"])->attributes(['class' => 'form-control','placeholder' => __('business.business_name'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('business.email'). ':', 'email')->attributes([])!!}
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                    </span>
                {!! html()->email('email', $settings["email"])->attributes(['class'=>'form-control', 'placeholder'=> __('business.email')])!!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                 {!! html()->label(__('business.currency') . ':', 'app_currency_id')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('app_currency_id', $currencies, $settings["app_currency_id"], ['class' => 'form-control select2','placeholder' => __('business.currency_placeholder'), 'required'])); !!}
            </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                 {!! html()->label(__('business.landmark') . ':', 'invoice_business_landmark')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('invoice_business_landmark', $settings["invoice_business_landmark"])->attributes(['class' => 'form-control','placeholder' => __('business.landmark'),'required']); !!}
            </div>
            </div>
        </div> 
        
        <div class="col-xs-4">
            <div class="form-group">
                 {!! html()->label(__('business.zip_code') . ':', 'invoice_business_zip')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('invoice_business_zip', $settings["invoice_business_zip"])->attributes(['class' => 'form-control','placeholder' => __('business.zip_code'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                 {!! html()->label(__('business.state') . ':', 'invoice_business_state')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('invoice_business_state', $settings["invoice_business_state"])->attributes(['class' => 'form-control','placeholder' => __('business.state'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                 {!! html()->label(__('business.city') . ':', 'invoice_business_city')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! html()->text('invoice_business_city', $settings["invoice_business_city"])->attributes(['class' => 'form-control','placeholder' => __('business.city'),'required']); !!}
            </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                 {!! html()->label(__('business.country') . ':', 'invoice_business_country')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-globe"></i>
                </span>
                {!! html()->text('invoice_business_country', $settings["invoice_business_country"])->attributes(['class' => 'form-control','placeholder' => __('business.country'), 'required']); !!}
            </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                 {!! html()->label(__('superadmin::lang.package_expiry_alert_days') . ':', 'package_expiry_alert_days')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-exclamation-triangle"></i>
                </span>
                {!! html()->input('number', 'package_expiry_alert_days', $settings["package_expiry_alert_days"])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.package_expiry_alert_days'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-xs-4">
            <div class="form-group">
                <label>
                    {!! html()->checkbox('enable_business_based_username', (int)$settings["enable_business_based_username"], 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_business_based_username' ) }}
                </label>
                <p class="help-block">@lang('superadmin::lang.business_based_username_help')</p>
            </div>
        </div>

        <div class="col-xs-12">
            <p class="help-block"><i>{!! __('superadmin::lang.version_info', ['version' => $superadmin_version]) !!}</i></p>
        </div>
    </div>
</div>