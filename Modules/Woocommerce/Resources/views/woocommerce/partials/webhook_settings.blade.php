<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_created')</h4>
        </div>
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('woocommerce::lang.webhook_secret') . ':', 'woocommerce_wh_oc_secret')->attributes([]) !!}
            	{!! html()->text('woocommerce_wh_oc_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_oc_secret : null)->attributes(['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderCreated'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_updated')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('woocommerce::lang.webhook_secret') . ':', 'woocommerce_wh_ou_secret')->attributes([]) !!}
                {!! html()->text('woocommerce_wh_ou_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_ou_secret : null)->attributes(['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderUpdated'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_deleted')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('woocommerce::lang.webhook_secret') . ':', 'woocommerce_wh_od_secret')->attributes([]) !!}
                {!! html()->text('woocommerce_wh_od_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_od_secret : null)->attributes(['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderDeleted'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_restored')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('woocommerce::lang.webhook_secret') . ':', 'woocommerce_wh_or_secret')->attributes([]) !!}
                {!! html()->text('woocommerce_wh_or_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_or_secret : null)->attributes(['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action([\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController::class, 'orderRestored'], ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

    </div>
</div>