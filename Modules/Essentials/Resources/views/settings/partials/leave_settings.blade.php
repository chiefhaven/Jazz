<div class="pos-tab-content active">
	<div class="row">
		<div class="col-xs-4">
            <div class="form-group">
            	{!! html()->label(__('essentials::lang.leave_ref_no_prefix') . ':', 'leave_ref_no_prefix')->attributes([]) !!}
            	{!! html()->text('leave_ref_no_prefix', !empty($settings['leave_ref_no_prefix']) ? $settings['leave_ref_no_prefix'] : null)->attributes(['class' => 'form-control','placeholder' => __('essentials::lang.leave_ref_no_prefix')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                {!! html()->label(__('essentials::lang.leave_instructions') . ':', 'leave_instructions')->attributes([]) !!}
                {!! html()->textarea('leave_instructions', !empty($settings['leave_instructions']) ? $settings['leave_instructions'] : null)->attributes(['class' => 'form-control','placeholder' => __('essentials::lang.leave_instructions')]); !!}
            </div>
        </div>
	</div>
</div>