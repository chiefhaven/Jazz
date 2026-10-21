<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.app_name') . ':', 'APP_NAME')->attributes([]) !!}
            	{!! html()->text('APP_NAME', $default_values['APP_NAME'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.app_name')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.app_title') . ':', 'APP_TITLE')->attributes([]) !!}
            	{!! html()->text('APP_TITLE', $default_values['APP_TITLE'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.app_title')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.app_default_language') . ':', 'APP_LOCALE')->attributes([]) !!}
            	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('APP_LOCALE', $languages, $default_values['APP_LOCALE'], ['class' => 'form-control'])); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label>
                {!! html()->checkbox('ALLOW_REGISTRATION', !empty($default_values["ALLOW_REGISTRATION"]), 1)->attributes([ 'class' => 'input-icheck']); !!}
                @lang('superadmin::lang.allow_registration')
                </label>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>
                {!! html()->checkbox('superadmin_enable_register_tc', !empty($settings["superadmin_enable_register_tc"]), 1)->attributes([ 'class' => 'input-icheck']); !!}
                @lang('superadmin::lang.enable_register_tc')
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.register_tc') .":", 'superadmin_register_tc')->attributes([]) !!}

                {!! html()->textarea('superadmin_register_tc', !empty($settings['superadmin_register_tc']) ? $settings['superadmin_register_tc'] : '')->attributes(['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.google_map_api_key') . ':', 'GOOGLE_MAP_API_KEY')->attributes([]) !!}
                {!! html()->text('GOOGLE_MAP_API_KEY', $default_values['GOOGLE_MAP_API_KEY'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.google_map_api_key')]); !!}
            </div>
        </div>
    </div>
</div>