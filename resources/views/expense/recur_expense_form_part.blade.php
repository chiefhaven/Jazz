<div class="box box-solid @if(!empty($expense->type) && $expense->type == 'expense_refund') hide @endif" id="recur_expense_div">
	<div class="box-body">
		<div class="row">
			<div class="col-md-4 col-sm-6">
				<br>
				<label>
	              {!! html()->checkbox('is_recurring', !empty($expense->is_recurring) == 1, 1)->attributes(['class' => 'input-icheck', 'id' => 'is_recurring']); !!} @lang('lang_v1.is_recurring')?
	            </label>@show_tooltip(__('lang_v1.recurring_expense_help'))
			</div>
			<div class="col-md-4 col-sm-6">
		        <div class="form-group">
		        	{!! html()->label(__('lang_v1.recur_interval') . ':*', 'recur_interval')->attributes([]) !!}
		        	<div class="input-group">
		               {!! html()->input('number', 'recur_interval', !empty($expense->recur_interval) ? $expense->recur_interval : null)->attributes(['class' => 'form-control', 'style' => 'width: 50%; z-index: 0;']); !!}
		               
		                {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('recur_interval_type', ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')], !empty($expense->recur_interval_type) ? $expense->recur_interval_type : 'days', ['class' => 'form-control', 'style' => 'width: 50%;z-index: 0;', 'id' => 'recur_interval_type'])); !!}
		                
		            </div>
		        </div>
		    </div>

		    <div class="col-md-4 col-sm-6">
		        <div class="form-group">
		        	{!! html()->label(__('lang_v1.no_of_repetitions') . ':', 'recur_repetitions')->attributes([]) !!}
		        	{!! html()->input('number', 'recur_repetitions', !empty($expense->recur_repetitions) ? $expense->recur_repetitions : null)->attributes(['class' => 'form-control']); !!}
			        <p class="help-block">@lang('lang_v1.recur_expense_repetition_help')</p>
		        </div>
		    </div>
		    @php
		    	$repetitions = [];
		    	for ($i=1; $i <= 30; $i++) { 
		    		$repetitions[$i] = str_ordinal($i);
		        }
		    @endphp
		    <div class="recur_repeat_on_div col-md-4 @if(empty($expense->recur_interval_type)) hide @elseif(!empty($expense->recur_interval_type) && $expense->recur_interval_type != 'months') hide @endif">
		        <div class="form-group">
		        	{!! html()->label(__('lang_v1.repeat_on') . ':', 'subscription_repeat_on')->attributes([]) !!}
		        	{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('subscription_repeat_on', $repetitions, !empty($expense->subscription_repeat_on) ? $expense->subscription_repeat_on : null, ['class' => 'form-control', 'placeholder' => __('messages.please_select')])); !!}
		        </div>
		    </div>
		</div>
	</div>
</div>