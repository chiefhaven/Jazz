<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.pusher_app_id') . ':', 'PUSHER_APP_ID')->attributes([]) !!}
            	{!! html()->text('PUSHER_APP_ID', $default_values['PUSHER_APP_ID'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.pusher_app_id')]); !!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.pusher_app_key') . ':', 'PUSHER_APP_KEY')->attributes([]) !!}
                {!! html()->text('PUSHER_APP_KEY', $default_values['PUSHER_APP_KEY'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.pusher_app_key')]); !!}

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.pusher_app_secret') . ':', 'PUSHER_APP_SECRET')->attributes([]) !!}
                {!! html()->text('PUSHER_APP_SECRET', $default_values['PUSHER_APP_SECRET'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.pusher_app_secret')]); !!}

            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! html()->label(__('superadmin::lang.pusher_app_cluster') . ':', 'PUSHER_APP_CLUSTER')->attributes([]) !!}
                {!! html()->text('PUSHER_APP_CLUSTER', $default_values['PUSHER_APP_CLUSTER'])->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.pusher_app_cluster')]); !!}

            </div>
        </div>
    </div>
</div>