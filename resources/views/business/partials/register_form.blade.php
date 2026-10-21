@if(empty($is_admin))
    <h3>@lang('business.business')</h3>
@endif
{!! html()->hidden('language', request()->lang)->attributes([]); !!}

<fieldset>
<legend class="text-black">@lang('business.business_details'):</legend>
<div class="col-md-12">
    <div class="form-group">
        {!! html()->label(__('business.business_name') . ':*', 'name')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-suitcase"></i>
            </span>
            {!! html()->text('name', null)->attributes(['class' => 'form-control','placeholder' => __('business.business_name'), 'required']); !!}
        </div>
    </div>
</div>
        
<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('business.start_date') . ':', 'start_date')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-calendar"></i>
        </span>
        {!! html()->text('start_date', null)->attributes(['class' => 'form-control start-date-picker','placeholder' => __('business.start_date'), 'readonly']); !!}
    </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('business.currency') . ':*', 'currency_id')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fas fa-money-bill-alt"></i>
        </span>
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('currency_id', $currencies, '', ['class' => 'form-control select2_register','placeholder' => __('business.currency_placeholder'), 'required'])); !!}
    </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('business.upload_logo') . ':', 'business_logo')->attributes([]) !!}
        {!! html()->file('business_logo')->attributes(['accept' => 'image/*']); !!}
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('lang_v1.website') . ':', 'website')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-globe"></i>
            </span>
            {!! html()->text('website', null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.website')]); !!}
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('lang_v1.business_telephone') . ':', 'mobile')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-phone"></i>
        </span>
        {!! html()->text('mobile', null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.business_telephone')]); !!}
    </div>
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('business.alternate_number') . ':', 'alternate_number')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-phone"></i>
            </span>
            {!! html()->text('alternate_number', null)->attributes(['class' => 'form-control','placeholder' => __('business.alternate_number')]); !!}
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('business.country') . ':*', 'country')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-globe"></i>
        </span>
        {!! html()->text('country', null)->attributes(['class' => 'form-control','placeholder' => __('business.country'), 'required']); !!}
    </div>
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('business.state') . ':*', 'state')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-map-marker"></i>
        </span>
        {!! html()->text('state', null)->attributes(['class' => 'form-control','placeholder' => __('business.state'), 'required']); !!}
    </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('business.city'). ':*', 'city')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-map-marker"></i>
        </span>
        {!! html()->text('city', null)->attributes(['class' => 'form-control','placeholder' => __('business.city'), 'required']); !!}
    </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('business.zip_code') . ':*', 'zip_code')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-map-marker"></i>
        </span>
        {!! html()->text('zip_code', null)->attributes(['class' => 'form-control','placeholder' => __('business.zip_code_placeholder'), 'required']); !!}
    </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-6">
    <div class="form-group">
    {!! html()->label(__('business.landmark') . ':*', 'landmark')->attributes([]) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="fa fa-map-marker"></i>
        </span>
        {!! html()->text('landmark', null)->attributes(['class' => 'form-control','placeholder' => __('business.landmark'), 'required']); !!}
    </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('business.time_zone') . ':*', 'time_zone')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fas fa-clock"></i>
            </span>
            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('time_zone', $timezone_list, config('app.timezone'), ['class' => 'form-control select2_register','placeholder' => __('business.time_zone'), 'required'])); !!}
        </div>
    </div>
</div>
</fieldset>

<!-- tax details -->
@if(empty($is_admin))
    <h3>@lang('business.business_settings')</h3>

    <fieldset>
    <legend class="text-black">@lang('business.business_settings'):</legend>
    <div class="col-md-6">
        <div class="form-group">
            {!! html()->label(__('business.tax_1_name') . ':', 'tax_label_1')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! html()->text('tax_label_1', null)->attributes(['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! html()->label(__('business.tax_1_no') . ':', 'tax_number_1')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! html()->text('tax_number_1', null)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! html()->label(__('business.tax_2_name') . ':', 'tax_label_2')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! html()->text('tax_label_2', null)->attributes(['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! html()->label(__('business.tax_2_no') . ':', 'tax_number_2')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! html()->text('tax_number_2', null)->attributes(['class' => 'form-control',]); !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-6">
        <div class="form-group">
            {!! html()->label(__('business.fy_start_month') . ':*', 'fy_start_month')->attributes([]) !!} @show_tooltip(__('tooltip.fy_start_month'))
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('fy_start_month', $months, null, ['class' => 'form-control select2_register', 'required', 'style' => 'width:100%;'])); !!}
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            {!! html()->label(__('business.accounting_method') . ':*', 'accounting_method')->attributes([]) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calculator"></i>
                </span>
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('accounting_method', $accounting_methods, null, ['class' => 'form-control select2_register', 'required', 'style' => 'width:100%;'])); !!}
            </div>
        </div>
    </div>
    </fieldset>
@endif

<!-- Owner Information -->
@if(empty($is_admin))
    <h3>@lang('business.owner')</h3>
@endif

<fieldset>
<legend class="text-black">@lang('business.owner_info')</legend>
<div class="col-md-4">
    <div class="form-group">
        {!! html()->label(__('business.prefix') . ':', 'surname')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-info"></i>
            </span>
            {!! html()->text('surname', null)->attributes(['class' => 'form-control','placeholder' => __('business.prefix_placeholder')]); !!}
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="form-group">
        {!! html()->label(__('business.first_name') . ':*', 'first_name')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-info"></i>
            </span>
            {!! html()->text('first_name', null)->attributes(['class' => 'form-control','placeholder' => __('business.first_name'), 'required']); !!}
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="form-group">
        {!! html()->label(__('business.last_name') . ':', 'last_name')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-info"></i>
            </span>
            {!! html()->text('last_name', null)->attributes(['class' => 'form-control','placeholder' =>  __('business.last_name')]); !!}
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('business.username') . ':*', 'username')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-user"></i>
            </span>
            {!! html()->text('username', null)->attributes(['class' => 'form-control','placeholder' => __('business.username'), 'required']); !!}
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('business.email') . ':*', 'email')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
            </span>
            {!! html()->text('email', null)->attributes(['class' => 'form-control','placeholder' => __('business.email'), 'required']); !!}
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('business.password') . ':*', 'password')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-lock"></i>
            </span>
            {!! html()->password('password')->attributes(['class' => 'form-control','placeholder' => __('business.password'), 'required']); !!}
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {!! html()->label(__('business.confirm_password') . ':*', 'confirm_password')->attributes([]) !!}
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-lock"></i>
            </span>
            {!! html()->password('confirm_password')->attributes(['class' => 'form-control','placeholder' => __('business.confirm_password'), 'required']); !!}
        </div>
    </div>
</div>
<div class="clearfix"></div>
    @if(!empty($system_settings['superadmin_enable_register_tc']) && !empty($is_register))
        <div class="col-md-6">
            <div>
                <label>
                    {!! html()->checkbox('accept_tc', false, 0)->attributes(['required', 'class' => 'input-check-box']); !!}
                    <a class="terms_condition cursor-pointer" data-toggle="modal" data-target="#tc_modal">
                        @lang('lang_v1.accept_terms_and_conditions') <i></i>
                    </a>
                </label>
            </div>
            @include('business.partials.terms_conditions')
        </div>
    @endif

    @if(config('constants.enable_recaptcha') && !empty($is_register))
        <div class="col-md-6">
            <div class="form-group">
                <div id="recaptcha-container"></div>
                @if ($errors->has('g-recaptcha-response'))
                    <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                @endif
            </div>
        </div>
    @endif
<div class="clearfix"></div>
</fieldset>
@if(config('constants.enable_recaptcha') && !empty($is_register))
    <script>
        window.RECAPTCHA_SITE_KEY = "{{ config('constants.google_recaptcha_key') }}";
    </script>
@endif
