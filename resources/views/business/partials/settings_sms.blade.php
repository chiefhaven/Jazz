@php
    $sms_service = isset($sms_settings['sms_service']) ? $sms_settings['sms_service'] : 'other';
@endphp
<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_service') . ':', 'sms_service')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sms_settings[sms_service]', ['nexmo' => 'Nexmo', 'twilio' => 'Twilio', 'other' => __('lang_v1.other')], $sms_service, ['class' => 'form-control', 'id' => 'sms_service'])); !!}
            </div>
        </div>
    </div>
    <div class="row sms_service_settings @if($sms_service != 'nexmo') hide @endif" data-service="nexmo">
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('lang_v1.nexmo_key') . ':', 'nexmo_key')->attributes([]) !!}
                {!! html()->text('sms_settings[nexmo_key]', !empty($sms_settings['nexmo_key']) ? $sms_settings['nexmo_key'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.nexmo_key'), 'id' => 'nexmo_key']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('lang_v1.nexmo_secret') . ':', 'nexmo_secret')->attributes([]) !!}
                {!! html()->text('sms_settings[nexmo_secret]', !empty($sms_settings['nexmo_secret']) ? $sms_settings['nexmo_secret'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.nexmo_secret'), 'id' => 'nexmo_secret']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('account.from') . ':', 'nexmo_from')->attributes([]) !!}
                {!! html()->text('sms_settings[nexmo_from]', !empty($sms_settings['nexmo_from']) ? $sms_settings['nexmo_from'] : null)->attributes(['class' => 'form-control','placeholder' => __('account.from'), 'id' => 'nexmo_from']); !!}
            </div>
        </div>
    </div>
    <div class="row sms_service_settings @if($sms_service != 'twilio') hide @endif" data-service="twilio">
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('lang_v1.twilio_sid') . ':', 'twilio_sid')->attributes([]) !!}
                {!! html()->text('sms_settings[twilio_sid]', !empty($sms_settings['twilio_sid']) ? $sms_settings['twilio_sid'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.twilio_sid'), 'id' => 'twilio_sid']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('lang_v1.twilio_token') . ':', 'twilio_token')->attributes([]) !!}
                {!! html()->text('sms_settings[twilio_token]', !empty($sms_settings['twilio_token']) ? $sms_settings['twilio_token'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.twilio_token'), 'id' => 'twilio_token']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('account.from') . ':', 'twilio_from')->attributes([]) !!}
                {!! html()->text('sms_settings[twilio_from]', !empty($sms_settings['twilio_from']) ? $sms_settings['twilio_from'] : null)->attributes(['class' => 'form-control','placeholder' => __('account.from'), 'id' => 'twilio_from']); !!}
            </div>
        </div>
    </div>
    <div class="row sms_service_settings @if($sms_service != 'other') hide @endif" data-service="other">
        <div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label('URL:', 'sms_settings_url')->attributes([]) !!}
            	{!! html()->text('sms_settings[url]', $sms_settings['url'])->attributes(['class' => 'form-control','placeholder' => 'URL', 'id' => 'sms_settings_url']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.send_to_param_name') . ':', 'send_to_param_name')->attributes([]) !!}
                <div class="input-group">
                    {!! html()->text('sms_settings[send_to_param_name]', !empty($sms_settings['send_to_param_name']) ? $sms_settings['send_to_param_name'] : null)->attributes(['class' => 'form-control', 'id' => 'send_to_param_name']); !!}
                    <div class="input-group-addon">
                        <select class="" name="sms_settings[send_to_param_type]">
                            <option value="string" @if (empty($sms_settings['send_to_param_type']) || $sms_settings['send_to_param_type'] == 'string') selected @endif>{{ __('lang_v1.string_default') }}</option>
                            <option value="array" @if (!empty($sms_settings['send_to_param_type']) && $sms_settings['send_to_param_type'] == 'array') selected @endif>{{ __('lang_v1.array') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.msg_param_name') . ':', 'msg_param_name')->attributes([]) !!}
                {!! html()->text('sms_settings[msg_param_name]', $sms_settings['msg_param_name'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.msg_param_name'), 'id' => 'msg_param_name']); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('lang_v1.request_method') . ':', 'request_method')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sms_settings[request_method]', ['get' => 'GET', 'post' => 'POST'], $sms_settings['request_method'], ['class' => 'form-control', 'id' => 'request_method'])); !!}
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                {!! html()->label(__('lang_v1.data_parameter_type') . ':', 'data_parameter_type')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('sms_settings[data_parameter_type]', ['form-data' => 'Form Data', 'json' => 'JSON'], !empty($sms_settings['data_parameter_type']) ? $sms_settings['data_parameter_type'] : 'form-data', ['class' => 'form-control', 'id' => 'data_parameter_type'])); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_header_key', ['number' => 1]) . ':', 'sms_settings_header_key1')->attributes([]) !!}
                {!! html()->text('sms_settings[header_1]', $sms_settings['header_1'] ?? null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_header_key', ['number' => 1]), 'id' => 'sms_settings_header_key1']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_header_val', ['number' => 1]) . ':', 'sms_settings_header_val1')->attributes([]) !!}
                {!! html()->text('sms_settings[header_val_1]', $sms_settings['header_val_1'] ?? null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_header_val', ['number' => 1]), 'id' => 'sms_settings_header_val1' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_header_key', ['number' => 2]) . ':', 'sms_settings_header_key2')->attributes([]) !!}
                {!! html()->text('sms_settings[header_2]', $sms_settings['header_2'] ?? null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_header_key', ['number' => 2]), 'id' => 'sms_settings_header_key2']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_header_val', ['number' => 2]) . ':', 'sms_settings_header_val2')->attributes([]) !!}
                {!! html()->text('sms_settings[header_val_2]', $sms_settings['header_val_2'] ?? null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_header_val', ['number' => 2]), 'id' => 'sms_settings_header_val2' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_header_key', ['number' => 3]) . ':', 'sms_settings_header_key3')->attributes([]) !!}
                {!! html()->text('sms_settings[header_3]', $sms_settings['header_3'] ?? null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_header_key', ['number' => 3]), 'id' => 'sms_settings_header_key3']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_header_val', ['number' => 3]) . ':', 'sms_settings_header_val3')->attributes([]) !!}
                {!! html()->text('sms_settings[header_val_3]', $sms_settings['header_val_3'] ?? null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_header_val', ['number' => 3]), 'id' => 'sms_settings_header_val3' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 1]) . ':', 'sms_settings_param_key1')->attributes([]) !!}
                {!! html()->text('sms_settings[param_1]', $sms_settings['param_1'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 1]), 'id' => 'sms_settings_param_key1']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 1]) . ':', 'sms_settings_param_val1')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_1]', $sms_settings['param_val_1'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 1]), 'id' => 'sms_settings_param_val1' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 2]) . ':', 'sms_settings_param_key2')->attributes([]) !!}
                {!! html()->text('sms_settings[param_2]', $sms_settings['param_2'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 2]), 'id' => 'sms_settings_param_key2']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 2]) . ':', 'sms_settings_param_val2')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_2]', $sms_settings['param_val_2'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 2]), 'id' => 'sms_settings_param_val2' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 3]) . ':', 'sms_settings_param_key3')->attributes([]) !!}
                {!! html()->text('sms_settings[param_3]', $sms_settings['param_3'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 3]), 'id' => 'sms_settings_param_key3']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 3]) . ':', 'sms_settings_param_val3')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_3]', $sms_settings['param_val_3'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 3]), 'id' => 'sms_settings_param_val3' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 4]) . ':', 'sms_settings_param_key4')->attributes([]) !!}
                {!! html()->text('sms_settings[param_4]', $sms_settings['param_4'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 4]), 'id' => 'sms_settings_param_key4']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 4]) . ':', 'sms_settings_param_val4')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_4]', $sms_settings['param_val_4'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 4]), 'id' => 'sms_settings_param_val4' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 5]) . ':', 'sms_settings_param_key5')->attributes([]) !!}
                {!! html()->text('sms_settings[param_5]', $sms_settings['param_5'])->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 5]), 'id' => 'sms_settings_param_key5']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 5]) . ':', 'sms_settings_param_val5')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_5]', $sms_settings['param_val_5'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 5]), 'id' => 'sms_settings_param_val5' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 6]) . ':', 'sms_settings_param_key6')->attributes([]) !!}
                {!! html()->text('sms_settings[param_6]', !empty($sms_settings['param_6']) ? $sms_settings['param_6'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 6]), 'id' => 'sms_settings_param_key6']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 6]) . ':', 'sms_settings_param_val6')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_6]', !empty($sms_settings['param_val_6']) ? $sms_settings['param_val_6'] : null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 6]), 'id' => 'sms_settings_param_val6' ]); !!}
            </div>
        </div>
         <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 7]) . ':', 'sms_settings_param_key7')->attributes([]) !!}
                {!! html()->text('sms_settings[param_7]', !empty($sms_settings['param_7']) ? $sms_settings['param_7'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 7]), 'id' => 'sms_settings_param_key7']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 7]) . ':', 'sms_settings_param_val7')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_7]', !empty($sms_settings['param_val_7']) ? $sms_settings['param_val_7'] : null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 7]), 'id' => 'sms_settings_param_val7' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 8]) . ':', 'sms_settings_param_key8')->attributes([]) !!}
                {!! html()->text('sms_settings[param_8]', !empty($sms_settings['param_8']) ? $sms_settings['param_8'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 8]), 'id' => 'sms_settings_param_key8']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 8]) . ':', 'sms_settings_param_val8')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_8]', !empty($sms_settings['param_val_8']) ? $sms_settings['param_val_8'] : null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 8]), 'id' => 'sms_settings_param_val8' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 9]) . ':', 'sms_settings_param_key9')->attributes([]) !!}
                {!! html()->text('sms_settings[param_9]', !empty($sms_settings['param_9']) ? $sms_settings['param_9'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 9]), 'id' => 'sms_settings_param_key9']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 9]) . ':', 'sms_settings_param_val9')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_9]', !empty($sms_settings['param_val_9']) ? $sms_settings['param_val_9'] : null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 9]), 'id' => 'sms_settings_param_val9' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_key', ['number' => 10]) . ':', 'sms_settings_param_key10')->attributes([]) !!}
                {!! html()->text('sms_settings[param_10]', !empty($sms_settings['param_10']) ? $sms_settings['param_10'] : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 10]), 'id' => 'sms_settings_param_key10']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.sms_settings_param_val', ['number' => 10]) . ':', 'sms_settings_param_val10')->attributes([]) !!}
                {!! html()->text('sms_settings[param_val_10]', !empty($sms_settings['param_val_10']) ? $sms_settings['param_val_10'] : null)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.sms_settings_param_val', ['number' => 10]), 'id' => 'sms_settings_param_val10' ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <div class="input-group">
                    {!! html()->text('test_number', null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.test_number'), 'id' => 'test_number']); !!}
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-success pull-right" id="test_sms_btn">@lang('lang_v1.test_sms_configuration')</button>
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>