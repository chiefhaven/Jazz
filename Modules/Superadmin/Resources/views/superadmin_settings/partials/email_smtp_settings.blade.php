<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.mail_driver') . ':', 'MAIL_MAILER')->attributes([]) !!}
            	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('MAIL_MAILER', $mail_drivers, $default_values['MAIL_MAILER'], ['class' => 'form-control'])); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.mail_host') . ':', 'MAIL_HOST')->attributes([]) !!}
            	{!! html()->text('MAIL_HOST', $default_values['MAIL_HOST'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.mail_host')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.mail_port') . ':', 'MAIL_PORT')->attributes([]) !!}
            	{!! html()->text('MAIL_PORT', $default_values['MAIL_PORT'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.mail_port')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.mail_username') . ':', 'MAIL_USERNAME')->attributes([]) !!}
                {!! html()->text('MAIL_USERNAME', $default_values['MAIL_USERNAME'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.mail_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.mail_password') . ':', 'MAIL_PASSWORD')->attributes([]) !!}
                {!! html()->text('MAIL_PASSWORD', $default_values['MAIL_PASSWORD'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.mail_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.mail_encryption') . ':', 'MAIL_ENCRYPTION')->attributes([]) !!}
                {!! html()->text('MAIL_ENCRYPTION', $default_values['MAIL_ENCRYPTION'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.mail_encryption_place')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.mail_from_address') . ':', 'MAIL_FROM_ADDRESS')->attributes([]) !!}
                {!! html()->email('MAIL_FROM_ADDRESS', $default_values['MAIL_FROM_ADDRESS'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.mail_from_address')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.mail_from_name') . ':', 'MAIL_FROM_NAME')->attributes([]) !!}
                {!! html()->text('MAIL_FROM_NAME', $default_values['MAIL_FROM_NAME'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.mail_from_name')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4">
            <div class="form-group">
                <label>
                {!! html()->checkbox('allow_email_settings_to_businesses', !empty($settings["allow_email_settings_to_businesses"]), 1)->attributes([ 'class' => 'input-icheck']); !!}
                @lang('superadmin::lang.allow_email_settings_to_businesses') 
                </label>
                @show_tooltip(__('superadmin::lang.allow_email_settings_tooltip'))
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>
                {!! html()->checkbox('enable_new_business_registration_notification', !empty($settings["enable_new_business_registration_notification"]), 1)->attributes([ 'class' => 'input-icheck']); !!}
                @lang('superadmin::lang.enable_new_business_registration_notification') 
                </label> @show_tooltip(__('superadmin::lang.new_business_notification_tooltip'))
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>
                {!! html()->checkbox('enable_new_subscription_notification', !empty($settings["enable_new_subscription_notification"]), 1)->attributes([ 'class' => 'input-icheck']); !!}
                @lang('superadmin::lang.enable_new_subscription_notification') 
                </label> @show_tooltip(__('superadmin::lang.new_subscription_tooltip'))
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <hr>
            <div class="form-group">
                <label>
                    {!! html()->checkbox('enable_welcome_email', isset($settings["enable_welcome_email"]) ? (int)$settings["enable_welcome_email"] : false, 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_welcome_email' ) }}
                </label> @show_tooltip(__('superadmin::lang.new_business_welcome_notification_tooltip'))
            </div>
        </div>
        <div class="col-xs-12">
            <h4>@lang('superadmin::lang.welcome_email_template'):</h4>
            <strong>@lang('lang_v1.available_tags'):</strong> {business_name}, {owner_name} <br><br>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.welcome_email_subject') . ':', 'welcome_email_subject')->attributes([]) !!}
                {!! html()->text('welcome_email_subject', isset($settings["welcome_email_subject"]) ? $settings["welcome_email_subject"] : '')->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.welcome_email_subject')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.welcome_email_body') . ':', 'welcome_email_body')->attributes([]) !!}
                {!! html()->textarea('welcome_email_body', isset($settings["welcome_email_body"]) ? $settings["welcome_email_body"] : '')->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.welcome_email_body')]); !!}
            </div>
        </div>
    </div>
</div>