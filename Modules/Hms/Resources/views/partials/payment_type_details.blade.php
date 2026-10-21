<div class="payment_details_div @if( $payment_line['method'] !== 'card' ) {{ 'hide' }} @endif" data-type="card" >
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_no'), "card_number_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][card_number]", $payment_line['card_number'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.card_no'), 'id' => "card_number_$row_index"]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_holder_name'), "card_holder_name_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][card_holder_name]", $payment_line['card_holder_name'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.card_holder_name'), 'id' => "card_holder_name_$row_index"]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_transaction_no'), "card_transaction_number_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][card_transaction_number]", $payment_line['card_transaction_number'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.card_transaction_no'), 'id' => "card_transaction_number_$row_index"]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_type'), "card_type_$row_index")->attributes([]) !!}
			{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })("payment[$row_index][card_type]", ['credit' => 'Credit Card', 'debit' => 'Debit Card','visa' => 'Visa', 'master' => 'MasterCard'], $payment_line['card_type'], ['class' => 'form-control', 'id' => "card_type_$row_index" ])); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.month'), "card_month_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][card_month]", $payment_line['card_month'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.month'),
			'id' => "card_month_$row_index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.year'), "card_year_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][card_year]", $payment_line['card_year'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.year'), 'id' => "card_year_$row_index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.security_code'), "card_security_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][card_security]", $payment_line['card_security'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.security_code'), 'id' => "card_security_$row_index"]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'cheque' ) {{ 'hide' }} @endif" data-type="cheque" >
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.cheque_no'), "cheque_number_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][cheque_number]", $payment_line['cheque_number'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.cheque_no'), 'id' => "cheque_number_$row_index"]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line['method'] !== 'bank_transfer' ) {{ 'hide' }} @endif" data-type="bank_transfer" >
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.bank_account_number'), "bank_account_number_$row_index")->attributes([]) !!}
			{!! html()->text("payment[$row_index][bank_account_number]", $payment_line['bank_account_number'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.bank_account_number'), 'id' => "bank_account_number_$row_index"]); !!}
		</div>
	</div>
</div>

@for ($i = 1; $i < 8; $i++)
<div class="payment_details_div @if( $payment_line['method'] !== 'custom_pay_' . $i ) {{ 'hide' }} @endif" data-type="custom_pay_{{$i}}" >
	<div class="col-md-6">
		<div class="form-group">
			{!! html()->label(__('lang_v1.transaction_no'), "transaction_no_{$i}_{$row_index}")->attributes([]) !!}
			{!! html()->text("payment[$row_index][transaction_no_{$i}]", $payment_line['transaction_no'])->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no'), 'id' => "transaction_no_{$i}_{$row_index}"]); !!}
		</div>
	</div>
</div>
@endfor