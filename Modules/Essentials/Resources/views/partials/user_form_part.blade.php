@component('components.widget', ['title' => __('essentials::lang.hrm_details')])
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
              {!! html()->label(__('essentials::lang.department') . ':', 'essentials_department_id')->attributes([]) !!}
              <div class="form-group">
                  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('essentials_department_id', $departments, !empty($user->essentials_department_id) ? $user->essentials_department_id : null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select') ])); !!}
              </div>
          </div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
            {!! html()->label(__('essentials::lang.designation') . ':', 'essentials_designation_id')->attributes([]) !!}
            <div class="form-group">
                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('essentials_designation_id', $designations, !empty($user->essentials_designation_id) ? $user->essentials_designation_id : null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select') ])); !!}
            </div>
        </div>
	</div>
</div>
@endcomponent
@component('components.widget', ['title' => __('essentials::lang.payroll')])
<div class="row">
    <div class="col-md-4">
        {!! html()->label(__('lang_v1.primary_work_location') . ':', 'location_id')->attributes([]) !!}
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $locations, !empty($user->location_id) ? $user->location_id : null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <div class="multi-input">
                {!! html()->label(__('essentials::lang.salary') . ':', 'essentials_salary')->attributes([]) !!}
                <br/>
                {!! html()->input('number', 'essentials_salary', !empty($user->essentials_salary) ? $user->essentials_salary : null)->attributes(['class' => 'form-control width-40 pull-left', 'placeholder' => __('essentials::lang.salary')]); !!}

                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('essentials_pay_period', ['month' => __('essentials::lang.per'). ' '.__('lang_v1.month'), 'week' => __('essentials::lang.per'). ' '.__('essentials::lang.week'), 'day' => __('essentials::lang.per'). ' '.__('lang_v1.day')], !empty($user->essentials_pay_period) ? $user->essentials_pay_period : null, ['class' => 'form-control width-60 pull-left'])); !!}
            </div>
        </div>
    </div>
    <div class="form-group col-md-4">
        {!! html()->label(__('essentials::lang.pay_components') . ':', 'pay_components')->attributes([]) !!}
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('pay_components[]', $pay_comoponenets, !empty($allowance_deduction_ids) ? $allowance_deduction_ids : [], ['class' => 'form-control select2', 'multiple' ])); !!}
    </div>
</div>
@endcomponent