<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-12">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.additional_js') . ':', 'additional_js')->attributes([]) !!} @show_tooltip(__('superadmin::lang.additional_js_instructions'))
            	{!! html()->textarea('additional_js', !empty($settings['additional_js']) ? $settings['additional_js'] : '')->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.additional_js')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
            	{!! html()->label(__('superadmin::lang.additional_css') . ':', 'additional_css')->attributes([]) !!} @show_tooltip(__('superadmin::lang.additional_css_instructions'))
            	{!! html()->textarea('additional_css', !empty($settings['additional_css']) ? $settings['additional_css'] : '')->attributes(['class' => 'form-control','placeholder' => __('superadmin::lang.additional_css')]); !!}
            </div>
        </div>
    </div>
</div>