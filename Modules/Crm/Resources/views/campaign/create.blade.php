@extends('layouts.app')

@section('title', __('crm::lang.campaigns'))

@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>
        @lang('crm::lang.campaigns')
        <small>@lang('lang_v1.create')</small>
    </h1>
</section>
<section class="content no-print">
    <div class="box box-solid">
        <div class="box-body">
            {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\Modules\Crm\Http\Controllers\CampaignController::class, 'store']), 'method' => 'post', 'id' => 'campaign_form' ]))->open() !!}
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.campaign_name') . ':*', 'name')->attributes([])!!}
                            {!! html()->text('name', null)->attributes(['class' => 'form-control', 'required' ]) !!}
                       </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.campaign_type') .':*', 'campaign_type')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('campaign_type', ['sms' => __('crm::lang.sms'), 'email' => __('business.email')], 'email', ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                </div>
                @if(!empty($contact_ids))
                    @php
                        $default_value = explode(',', $contact_ids);
                        $to = 'contact';
                    @endphp
                @else
                    @php
                        $default_value = null;
                        $to = null;
                    @endphp
                @endif
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.to').':*', 'to')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('to', ['customer' => __('lang_v1.customers'), 'lead' => __('crm::lang.leads'), 'transaction_activity' => __('crm::lang.transaction_activity'), 'contact' => __('contact.contact')], $to, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-8 customer_div" style="display: none;">
                        <div class="form-group">
                            {!! html()->label(__('lang_v1.customers') .':*', 'contact_id')->attributes([]) !!}
                            <button type="button" class="btn btn-primary btn-xs select-all">
                                @lang('lang_v1.select_all')
                            </button>
                            <button type="button" class="btn btn-primary btn-xs deselect-all">
                                @lang('lang_v1.deselect_all')
                            </button>
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('contact_id[]', $customers, null, ['class' => 'form-control select2', 'multiple', 'id' => 'contact_id', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-8 lead_div" style="display: none;">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.leads') .':*', 'lead_id')->attributes([]) !!}
                            <button type="button" class="btn btn-primary btn-xs select-all">
                                @lang('lang_v1.select_all')
                            </button>
                            <button type="button" class="btn btn-primary btn-xs deselect-all">
                                @lang('lang_v1.deselect_all')
                            </button>
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('lead_id[]', $leads, null, ['class' => 'form-control select2', 'multiple', 'id' => 'lead_id', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-8 contact_div" style="display: none;">
                        <div class="form-group">
                            {!! html()->label(__('contact.contact') .':*', 'contact')->attributes([]) !!}
                            <button type="button" class="btn btn-primary btn-xs select-all">
                                @lang('lang_v1.select_all')
                            </button>
                            <button type="button" class="btn btn-primary btn-xs deselect-all">
                                @lang('lang_v1.deselect_all')
                            </button>
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('contact[]', $contacts, $default_value, ['class' => 'form-control select2', 'multiple', 'id' => 'contact', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-4 transaction_activity_div" style="display: none;">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.transaction_activity').':*', 'trans_activity')->attributes([]) !!}
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('trans_activity', ['has_transactions' => __('crm::lang.has_transactions'), 'has_no_transactions' => __('crm::lang.has_no_transactions')], null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;'])); !!}
                        </div>
                    </div>
                    <div class="col-md-4 transaction_activity_div" style="display: none;">
                        <div class="form-group">
                            <label for="in_days">{{__('crm::lang.in_days')}}:*</label>
                            <div class="input-group">
                                <div class="input-group-addon">{{__('crm::lang.in')}}</div>
                                    <input type="text" class="form-control input_number" id="in_days" placeholder="0" name="in_days" required>
                                <div class="input-group-addon">{{__('lang_v1.days')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="row email_div">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.subject') . ':*', 'subject')->attributes([])!!}
                            {!! html()->text('subject', null)->attributes(['class' => 'form-control', 'required' ]) !!}
                       </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.email_body') . ':*', 'email_body')->attributes([]) !!}
                            {!! html()->textarea('email_body', null)->attributes(['class' => 'form-control ', 'id' => 'email_body', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="row sms_div">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label(__('crm::lang.sms_body') . ':', 'sms_body')->attributes([]) !!}
                            {!! html()->textarea('sms_body', null)->attributes(['class' => 'form-control ', 'id' => 'sms_body', 'rows' => '6', 'required']); !!}
                        </div>
                    </div>
                </div>
                <strong>@lang('lang_v1.available_tags'):</strong>
                <p class="help-block">
                    {{implode(', ', $tags)}}
                </p>

                <button type="submit" class="btn btn-primary btn-sm pull-right submit-button draft m-5" name="send_notification" value="0" data-style="expand-right">
                    <span class="ladda-label">
                        <i class="fas fa-save"></i>
                        @lang('sale.draft')
                    </span>
                </button>

                <button type="submit" class="btn btn-warning btn-sm pull-right submit-button notif m-5" name="send_notification" value="1" data-style="expand-right">
                    <span class="ladda-label">
                        <i class="fas fa-envelope-square"></i>
                        @lang('crm::lang.send_notification')
                    </span>
                </button>

            {!! html()->form()->close() !!}
        </div>
    </div>
@stop
@section('javascript')
    <script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(function () {

            $('select#to').change(function() {
                toggleFieldBasedOnTo($(this).val());
            });

            function toggleFieldBasedOnTo (to) {
                if (to == 'customer') {
                    $('div.customer_div').show();
                    $('div.lead_div').hide();
                    $('div.transaction_activity_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'lead') {
                    $('div.lead_div').show();
                    $('div.customer_div').hide();
                    $('div.transaction_activity_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'transaction_activity') {
                    $('div.transaction_activity_div').show();
                    $('div.customer_div').hide();
                    $('div.lead_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'contact') {
                    $('div.contact_div').show();
                    $('div.transaction_activity_div').hide();
                    $('div.customer_div').hide();
                    $('div.lead_div').hide();
                } else {
                    $('div.transaction_activity_div, div.customer_div, div.lead_div, div.contact_div').hide();
                }
            }

            toggleFieldBasedOnTo($('select#to').val());
        });
    </script>
@endsection