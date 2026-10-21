<div class="pos-tab-content">
	<div class="row">
		<div class="col-xs-4">
            <div class="form-group">
                {!! html()->label(__('essentials::lang.payroll_ref_no_prefix') . ':', 'payroll_ref_no_prefix')->attributes([]) !!}
                {!! html()->text('payroll_ref_no_prefix', !empty($settings['payroll_ref_no_prefix']) ? $settings['payroll_ref_no_prefix'] : null)->attributes(['class' => 'form-control','placeholder' => __('essentials::lang.payroll_ref_no_prefix')]); !!}
            </div>
        </div>
	</div>
</div>