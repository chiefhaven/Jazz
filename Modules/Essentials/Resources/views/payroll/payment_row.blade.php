{!! html()->label(__('lang_v1.paid_on') . ':*', "paid_on_".$id)->attributes([]) !!}
<div class="input-group">
  <span class="input-group-addon">
    <i class="fa fa-calendar"></i>
  </span>
  {!! html()->text('payments['.$id.'][paid_on]', @format_datetime($payroll['paid_on']))->attributes(['class' => 'form-control input-sm paid_on', 'readonly', 'required', 'id' => 'paid_on_'.$id, 'data-id' => $id]); !!}
</div>

@if(!empty($accounts))
	{!! html()->label(__('lang_v1.payment_account') . ':', "account_id_".$id)->attributes([]) !!}
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fas fa-money-bill-alt"></i>
        </span>
        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('payments['.$id.'][account_id]', $accounts, null, ['class' => 'form-control input-sm select2', 'id' => "account_id_".$id, 'style' => 'width:100%;', 'data-id' => $id])); !!}
      </div>
@endif

{!! html()->label(__('purchase.payment_method') . ':*', "method_".$id)->attributes([]) !!}
<div class="input-group">
  <span class="input-group-addon">
    <i class="fas fa-money-bill-alt"></i>
  </span>
  {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('payments['.$id.'][method]', $payment_types, null, ['class' => 'form-control select2 payment_types input-sm', 'id' => 'method_'.$id, 'style' => 'width:100%;', 'data-id' => $id, 'placeholder' => __('messages.please_select')])); !!}
</div>

<div id="cheque_{{$id}}" style="display: none;">
	{!! html()->label(__('lang_v1.cheque_no'), "cheque_number_".$id)->attributes([]) !!}
	{!! html()->text('payments['.$id.'][cheque_number]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.cheque_no'), 'id' => 'cheque_number_'.$id, 'data-id' => $id]); !!}
</div>

<div id="bank_transfer_{{$id}}" style="display: none;">
	{!! html()->label(__('lang_v1.bank_account_number'), "bank_account_number_".$id)->attributes([]) !!}
	{!! html()->text('payments['.$id.'][bank_account_number]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.bank_account_number'), 'data-id' => $id, 'id' => 'bank_account_number_'.$id]); !!}
</div>
<div id="custom_pay_1_{{$id}}" style="display: none;">
	{!! html()->label(__('lang_v1.transaction_no'), "transaction_no_1_".$id)->attributes([]) !!}
	{!! html()->text('payments['.$id.'][transaction_no_1]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.transaction_no'), 'data-id' => $id, 'id' => 'transaction_no_1_'.$id]); !!}
</div>
<div id="custom_pay_2_{{$id}}" style="display: none;">
	{!! html()->label(__('lang_v1.transaction_no'), "transaction_no_2_".$id)->attributes([]) !!}
	{!! html()->text('payments['.$id.'][transaction_no_2]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.transaction_no'), 'data-id' => $id, 'id' => 'transaction_no_2_'.$id]); !!}
</div>
<div id="custom_pay_3_{{$id}}" style="display: none;">
	{!! html()->label(__('lang_v1.transaction_no'), "transaction_no_3_".$id)->attributes([]) !!}
	{!! html()->text('payments['.$id.'][transaction_no_3]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.transaction_no'), 'data-id' => $id, 'id' => 'transaction_no_3_'.$id]); !!}
</div>
<div id="card_{{$id}}" style="display: none;">
    <table class="table">
        <tr>
            <td>
            	{!! html()->label(__('lang_v1.card_no'), "card_number")->attributes([]) !!}
				{!! html()->text('payments['.$id.'][card_number]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.card_no')]); !!}
            </td>
            <td>
            	{!! html()->label(__('lang_v1.card_holder_name'), "card_holder_name")->attributes([]) !!}
				{!! html()->text('payments['.$id.'][card_holder_name]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.card_holder_name')]); !!}
        	</td>
        </tr>
        
        <tr>
            <td>
            	{!! html()->label(__('lang_v1.card_transaction_no'), "card_transaction_number")->attributes([]) !!}
				{!! html()->text('payments['.$id.'][card_transaction_number]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.card_transaction_no')]); !!}
            </td>
            <td>
            	{!! html()->label(__('lang_v1.card_type'), "card_type")->attributes([]) !!}
				{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('payments['.$id.'][card_type]', ['credit' => 'Credit Card', 'debit' => 'Debit Card', 'visa' => 'Visa', 'master' => 'MasterCard'], null, ['class' => 'form-control input-sm'])); !!}
            </td>
        </tr>
        <tr>
        	<td>
        		{!! html()->label(__('lang_v1.month'), "card_month")->attributes([]) !!}
				{!! html()->text('payments['.$id.'][card_month]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.month') ]); !!}
        	</td>
        	<td>
        		{!! html()->label(__('lang_v1.year'), "card_year")->attributes([]) !!}
				{!! html()->text('payments['.$id.'][card_year]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.year') ]); !!}
        	</td>
        </tr>
        <tr>
        	<td colspan="2">
        		{!! html()->label(__('lang_v1.security_code'), "card_security")->attributes([]) !!}
				{!! html()->text('payments['.$id.'][card_security]', null)->attributes(['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.security_code')]); !!}
        	</td>
        </tr>
	</table>
</div>