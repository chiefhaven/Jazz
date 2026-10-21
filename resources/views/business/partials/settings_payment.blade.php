<!--payment related settings -->
<div class="pos-tab-content">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! html()->label(__('lang_v1.cash_denominations') . ':', 'cash_denominations')->attributes([]) !!}
                 {!! html()->text('pos_settings[cash_denominations]', isset($pos_settings['cash_denominations']) ? $pos_settings['cash_denominations'] : null)->attributes(['class' => 'form-control', 'id' => 'cash_denominations']); !!}
                 <p class="help-block">{{__('lang_v1.cash_denominations_help')}}</p>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.enable_cash_denomination_on') . ':', 'enable_cash_denomination_on')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('pos_settings[enable_cash_denomination_on]', ['pos_screen' => __('lang_v1.pos_screen'), 'all_screens' => __('lang_v1.all_screen')], isset($pos_settings['enable_cash_denomination_on']) ? $pos_settings['enable_cash_denomination_on'] : 'pos_screen', ['class' => 'form-control', 'style' => 'width: 100%;' ])); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! html()->label(__('lang_v1.enable_cash_denomination_for_payment_methods') . ':', 'enable_cash_denomination_for_payment_methods')->attributes([]) !!}
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('pos_settings[enable_cash_denomination_for_payment_methods][]', $payment_types, isset($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'multiple' ])); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! html()->checkbox('pos_settings[cash_denomination_strict_check]', !empty($pos_settings['cash_denomination_strict_check']), 1)->attributes([ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.strict_check' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.strict_check_help'))
                </div>
            </div>
        </div>
    </div>
</div>