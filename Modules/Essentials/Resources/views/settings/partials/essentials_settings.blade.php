<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('essentials::lang.essentials_todos_prefix') . ':', 'essentials_todos_prefix')->attributes([]) !!}
                {!! html()->text('essentials_todos_prefix', !empty($settings['essentials_todos_prefix']) ? $settings['essentials_todos_prefix'] : null)->attributes(['class' => 'form-control','placeholder' => __('essentials::lang.essentials_todos_prefix')]); !!}
            </div>
        </div>
    </div>
</div>