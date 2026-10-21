<div class="pos-tab-content">
     <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('business.view_stock_expiry_alert_for') . ':*', 'stock_expiry_alert_days')->attributes([]) !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-calendar-times"></i>
                </span>
                {!! html()->input('number', 'stock_expiry_alert_days', $business->stock_expiry_alert_days)->attributes(['class' => 'form-control','required']); !!}
                <span class="input-group-addon">
                    @lang('business.days')
                </span>
                </div>
            </div>
        </div>
    </div>
</div>