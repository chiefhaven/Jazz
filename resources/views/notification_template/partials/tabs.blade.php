<!-- Custom Tabs -->
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        @foreach($templates as $key => $value)
            <li @if($loop->index == 0) class="active" @endif>
                <a href="#cn_{{$key}}" data-toggle="tab" aria-expanded="true">
                {{$value['name']}} </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($templates as $key => $value)
            <div class="tab-pane @if($loop->index == 0) active @endif" id="cn_{{$key}}">
                <div class="row">
                <div class="col-md-12">
                    @if(!empty($value['extra_tags']))
                        <strong>@lang('lang_v1.available_tags'):</strong>
                        @include('notification_template.partials.tags', ['tags' => $value['extra_tags']])
                    
                    @endif
                    @if(!empty($value['help_text']))
                    <p class="help-block">{{$value['help_text']}}</p>
                    @endif
                </div>
                <div class="col-md-12 mt-10">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.email_subject').':', $key . '_subject')->attributes([]) !!}
                        {!! html()->text('template_data[' . $key . '][subject]', $value['subject'])->attributes(['class' => 'form-control'
                        , 'placeholder' => __('lang_v1.email_subject'), 'id' => $key . '_subject']); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! html()->label('CC:', $key . '_cc')->attributes([]) !!}
                        {!! html()->email('template_data[' . $key . '][cc]', $value['cc'])->attributes(['class' => 'form-control'
                        , 'placeholder' => 'CC', 'id' => $key . '_cc']); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! html()->label('BCC:', $key . '_bcc')->attributes([]) !!}
                        {!! html()->email('template_data[' . $key . '][bcc]', $value['bcc'])->attributes(['class' => 'form-control'
                        , 'placeholder' => 'BCC', 'id' => $key . '_bcc']); !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.email_body').':', $key . '_email_body')->attributes([]) !!}
                        {!! html()->textarea('template_data[' . $key . '][email_body]', $value['email_body'])->attributes(['class' => 'form-control ckeditor'
                        , 'placeholder' => __('lang_v1.email_body'), 'id' => $key . '_email_body', 'rows' => 6]); !!}
                    </div>
                </div>
                <div class="col-md-12 @if($key == 'send_ledger') hide @endif">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.sms_body').':', $key . '_sms_body')->attributes([]) !!}
                        {!! html()->textarea('template_data[' . $key . '][sms_body]', $value['sms_body'])->attributes(['class' => 'form-control'
                        , 'placeholder' => __('lang_v1.sms_body'), 'id' => $key . '_sms_body', 'rows' => 6]); !!}
                    </div>
                </div>
                <div class="col-md-12 @if($key == 'send_ledger') hide @endif">
                    <div class="form-group">
                        {!! html()->label(__('lang_v1.whatsapp_text').':', $key . '_whatsapp_text')->attributes([]) !!}
                        {!! html()->textarea('template_data[' . $key . '][whatsapp_text]', $value['whatsapp_text'])->attributes(['class' => 'form-control'
                        , 'placeholder' => __('lang_v1.whatsapp_text'), 'id' => $key . '_whatsapp_text', 'rows' => 6]); !!}
                    </div>
                </div>
                @if($key == 'new_sale' || $key == 'payment_reminder')
                    <div class="col-md-12 mt-15">
                        <div class="form-group">
                            <label class="checkbox-inline">
                                {!! html()->checkbox('template_data[' . $key . '][auto_send]', $value['auto_send'], 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.autosend_email')
                            </label>
                            <label class="checkbox-inline">
                                {!! html()->checkbox('template_data[' . $key . '][auto_send_sms]', $value['auto_send_sms'], 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.autosend_sms')
                            </label>
                            <label class="checkbox-inline">
                                {!! html()->checkbox('template_data[' . $key . '][auto_send_wa_notif]', $value['auto_send_wa_notif'], 1)->attributes(['class' => 'input-icheck']); !!} @lang('lang_v1.auto_send_wa_notif')
                            </label>
                        </div>
                        @if($key == 'payment_reminder')
                            <p class="help-block">@lang('lang_v1.payment_reminder_help')</p>

                        @elseif($key == 'new_sale')
                            <p class="help-block">@lang('lang_v1.new_sale_notification_help')</p>
                        @endif
                    </div>
                @endif
                </div>
            </div>
        @endforeach
    </div>
</div>