<div class="pos-tab-content">
    <div class="row">
        <div id="toggle_visibility" @if(!empty($eis_settings['use_superadmin_settings'])) class="hide" @endif>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('terminal_activation_code', __('lang_v1.terminal_activation_code') . ':') !!}
            	{!! Form::text('eis_settings[terminal_activation_code]', $eis_settings['terminal_activation_code'], ['class' => 'form-control','placeholder' => __('lang_v1.terminal_activation_code'), 'id' => 'terminal_activation_code']); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12 activate_eis_terminal_btn @if(!empty($eis_settings['use_superadmin_settings'])) hide @endif">
            <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white  pull-right" id="activate_eis_terminal_btn">@lang('lang_v1.activate_eis_terminal')</button>
        </div>
    </div>
</div>