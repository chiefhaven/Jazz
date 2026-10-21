<!--Purchase related settings -->
<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.default_credit_limit') . ':', 'default_credit_limit')->attributes([]) !!}
                {!! html()->text('common_settings[default_credit_limit]', $common_settings['default_credit_limit'] ?? '')->attributes(['class' => 'form-control input_number',
                'placeholder' => __('lang_v1.default_credit_limit'), 'id' => 'default_credit_limit']); !!}
            </div>
        </div>
    </div>
</div>