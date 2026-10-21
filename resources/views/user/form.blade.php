@php
  $custom_labels = json_decode(session('business.custom_labels'), true);
  $user_custom_field1 = !empty($custom_labels['user']['custom_field_1']) ? $custom_labels['user']['custom_field_1'] : __('lang_v1.user_custom_field1');
  $user_custom_field2 = !empty($custom_labels['user']['custom_field_2']) ? $custom_labels['user']['custom_field_2'] : __('lang_v1.user_custom_field2');
  $user_custom_field3 = !empty($custom_labels['user']['custom_field_3']) ? $custom_labels['user']['custom_field_3'] : __('lang_v1.user_custom_field3');
  $user_custom_field4 = !empty($custom_labels['user']['custom_field_4']) ? $custom_labels['user']['custom_field_4'] : __('lang_v1.user_custom_field4');
@endphp
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.dob' ) . ':', 'user_dob')->attributes([]) !!}
    {!! html()->text('dob', !empty($user->dob) ? @format_date($user->dob) : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.dob'), 'readonly', 'id' => 'user_dob' ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.gender' ) . ':', 'gender')->attributes([]) !!}
    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('gender', ['male' => __('lang_v1.male'), 'female' => __('lang_v1.female'), 'others' => __('lang_v1.others')], !empty($user->gender) ? $user->gender : null, ['class' => 'form-control', 'id' => 'gender', 'placeholder' => __( 'messages.please_select') ])); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.marital_status' ) . ':', 'marital_status')->attributes([]) !!}
    {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('marital_status', ['married' => __( 'lang_v1.married'), 'unmarried' => __( 'lang_v1.unmarried' ), 'divorced' => __( 'lang_v1.divorced' )], !empty($user->marital_status) ? $user->marital_status : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.marital_status') ])); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.blood_group' ) . ':', 'blood_group')->attributes([]) !!}
    {!! html()->text('blood_group', !empty($user->blood_group) ? $user->blood_group : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.blood_group') ]); !!}
</div>
<div class="clearfix"></div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.mobile_number' ) . ':', 'contact_number')->attributes([]) !!}
    {!! html()->text('contact_number', !empty($user->contact_number) ? $user->contact_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.mobile_number') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'business.alternate_number' ) . ':', 'alt_number')->attributes([]) !!}
    {!! html()->text('alt_number', !empty($user->alt_number) ? $user->alt_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'business.alternate_number') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.family_contact_number' ) . ':', 'family_number')->attributes([]) !!}
    {!! html()->text('family_number', !empty($user->family_number) ? $user->family_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.family_contact_number') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.fb_link' ) . ':', 'fb_link')->attributes([]) !!}
    {!! html()->text('fb_link', !empty($user->fb_link) ? $user->fb_link : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.fb_link') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.twitter_link' ) . ':', 'twitter_link')->attributes([]) !!}
    {!! html()->text('twitter_link', !empty($user->twitter_link) ? $user->twitter_link : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.twitter_link') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.social_media', ['number' => 1] ) . ':', 'social_media_1')->attributes([]) !!}
    {!! html()->text('social_media_1', !empty($user->social_media_1) ? $user->social_media_1 : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.social_media', ['number' => 1] ) ]); !!}
</div>
<div class="clearfix"></div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.social_media', ['number' => 2] ) . ':', 'social_media_2')->attributes([]) !!}
    {!! html()->text('social_media_2', !empty($user->social_media_2) ? $user->social_media_2 : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.social_media', ['number' => 2] ) ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label($user_custom_field1 . ':', 'custom_field_1')->attributes([]) !!}
    {!! html()->text('custom_field_1', !empty($user->custom_field_1) ? $user->custom_field_1 : null)->attributes(['class' => 'form-control', 'placeholder' => $user_custom_field1 ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label($user_custom_field2 . ':', 'custom_field_2')->attributes([]) !!}
    {!! html()->text('custom_field_2', !empty($user->custom_field_2) ? $user->custom_field_2 : null)->attributes(['class' => 'form-control', 'placeholder' => $user_custom_field2 ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label($user_custom_field3 . ':', 'custom_field_3')->attributes([]) !!}
    {!! html()->text('custom_field_3', !empty($user->custom_field_3) ? $user->custom_field_3 : null)->attributes(['class' => 'form-control', 'placeholder' => $user_custom_field3 ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label($user_custom_field4 . ':', 'custom_field_4')->attributes([]) !!}
    {!! html()->text('custom_field_4', !empty($user->custom_field_4) ? $user->custom_field_4 : null)->attributes(['class' => 'form-control', 'placeholder' => $user_custom_field4 ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.guardian_name') . ':', 'guardian_name')->attributes([]) !!}
    {!! html()->text('guardian_name', !empty($user->guardian_name) ? $user->guardian_name : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.guardian_name' ) ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.id_proof_name') . ':', 'id_proof_name')->attributes([]) !!}
    {!! html()->text('id_proof_name', !empty($user->id_proof_name) ? $user->id_proof_name : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.id_proof_name' ) ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.id_proof_number') . ':', 'id_proof_number')->attributes([]) !!}
    {!! html()->text('id_proof_number', !empty($user->id_proof_number) ? $user->id_proof_number : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.id_proof_number' ) ]); !!}
</div>
<div class="clearfix"></div>
<div class="form-group col-md-6">
    {!! html()->label(__( 'lang_v1.permanent_address') . ':', 'permanent_address')->attributes([]) !!}
    {!! html()->textarea('permanent_address', !empty($user->permanent_address) ? $user->permanent_address : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.permanent_address'), 'rows' => 3 ]); !!}
</div>
<div class="form-group col-md-6">
    {!! html()->label(__( 'lang_v1.current_address') . ':', 'current_address')->attributes([]) !!}
    {!! html()->textarea('current_address', !empty($user->current_address) ? $user->current_address : null)->attributes(['class' => 'form-control', 'placeholder' => __( 'lang_v1.current_address'), 'rows' => 3 ]); !!}
</div>
<div class="col-md-12">
    <hr>
    <h4>@lang('lang_v1.bank_details'):</h4>
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.account_holder_name') . ':', 'account_holder_name')->attributes([]) !!}
    {!! html()->text('bank_details[account_holder_name]', !empty($bank_details['account_holder_name']) ? $bank_details['account_holder_name'] : null)->attributes(['class' => 'form-control', 'id' => 'account_holder_name', 'placeholder' => __( 'lang_v1.account_holder_name') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.account_number') . ':', 'account_number')->attributes([]) !!}
    {!! html()->text('bank_details[account_number]', !empty($bank_details['account_number']) ? $bank_details['account_number'] : null)->attributes(['class' => 'form-control', 'id' => 'account_number', 'placeholder' => __( 'lang_v1.account_number') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.bank_name') . ':', 'bank_name')->attributes([]) !!}
    {!! html()->text('bank_details[bank_name]', !empty($bank_details['bank_name']) ? $bank_details['bank_name'] : null)->attributes(['class' => 'form-control', 'id' => 'bank_name', 'placeholder' => __( 'lang_v1.bank_name') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.bank_code') . ':', 'bank_code')->attributes([]) !!} @show_tooltip(__('lang_v1.bank_code_help'))
    {!! html()->text('bank_details[bank_code]', !empty($bank_details['bank_code']) ? $bank_details['bank_code'] : null)->attributes(['class' => 'form-control', 'id' => 'bank_code', 'placeholder' => __( 'lang_v1.bank_code') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.branch') . ':', 'branch')->attributes([]) !!}
    {!! html()->text('bank_details[branch]', !empty($bank_details['branch']) ? $bank_details['branch'] : null)->attributes(['class' => 'form-control', 'id' => 'branch', 'placeholder' => __( 'lang_v1.branch') ]); !!}
</div>
<div class="form-group col-md-3">
    {!! html()->label(__( 'lang_v1.tax_payer_id') . ':', 'tax_payer_id')->attributes([]) !!}
    @show_tooltip(__('lang_v1.tax_payer_id_help'))
    {!! html()->text('bank_details[tax_payer_id]', !empty($bank_details['tax_payer_id']) ? $bank_details['tax_payer_id'] : null)->attributes(['class' => 'form-control', 'id' => 'tax_payer_id', 'placeholder' => __( 'lang_v1.tax_payer_id') ]); !!}
</div>