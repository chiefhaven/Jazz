<div class="pos-tab-content">
    <div class="row">
        @if(!empty($allow_superadmin_email_settings))
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! html()->checkbox('email_settings[use_superadmin_settings]', !empty($email_settings['use_superadmin_settings']), 1)->attributes([ 'class' => 'input-icheck', 'id' => 'use_superadmin_settings']); !!} {{ __( 'lang_v1.use_superadmin_email_settings' ) }}
                  </label>
                </div>
            </div>
        </div>
        @endif
        <div id="toggle_visibility" @if(!empty($email_settings['use_superadmin_settings'])) class="hide" @endif>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.mail_driver') . ':', 'mail_driver')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('email_settings[mail_driver]', $mail_drivers, !empty($email_settings['mail_driver']) ? $email_settings['mail_driver'] : 'smtp', ['class' => 'form-control', 'id' => 'mail_driver'])); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('lang_v1.mail_host') . ':', 'mail_host')->attributes([]) !!}
            	{!! html()->text('email_settings[mail_host]', $email_settings['mail_host'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.mail_host'), 'id' => 'mail_host']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('lang_v1.mail_port') . ':', 'mail_port')->attributes([]) !!}
            	{!! html()->text('email_settings[mail_port]', $email_settings['mail_port'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.mail_port'), 'id' => 'mail_port']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.mail_username') . ':', 'mail_username')->attributes([]) !!}
                {!! html()->text('email_settings[mail_username]', $email_settings['mail_username'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.mail_username'), 'id' => 'mail_username']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.mail_password') . ':', 'mail_password')->attributes([]) !!}
                <input type="password" name="email_settings[mail_password]" value="{{$email_settings['mail_password']}}" class="form-control" placeholder="{{__('lang_v1.mail_password')}}", id="mail_password">
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.mail_encryption') . ':', 'mail_encryption')->attributes([]) !!}
                {!! html()->text('email_settings[mail_encryption]', $email_settings['mail_encryption'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.mail_encryption_place'), 'id' => 'mail_encryption']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.mail_from_address') . ':', 'mail_from_address')->attributes([]) !!}
                {!! html()->email('email_settings[mail_from_address]', $email_settings['mail_from_address'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.mail_from_address'), 'id' => 'mail_from_address' ]); !!}
            </div>
        </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.mail_from_name') . ':', 'mail_from_name')->attributes([]) !!}
                {!! html()->text('email_settings[mail_from_name]', $email_settings['mail_from_name'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.mail_from_name'), 'id' => 'mail_from_name']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12 test_email_btn @if(!empty($email_settings['use_superadmin_settings'])) hide @endif">
            <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white  pull-right" id="test_email_btn">@lang('lang_v1.test_email_configuration')</button>
        </div>
    </div>
</div>