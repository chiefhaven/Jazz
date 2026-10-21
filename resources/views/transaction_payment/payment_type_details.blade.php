<div class="payment_details_div @if( $payment_line->method !== 'card' ) {{ 'hide' }} @endif" data-type="card" >
	<div class="col-md-4">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_no'), "card_number")->attributes([]) !!}
			{!! html()->text("card_number", $payment_line->card_number)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.card_no')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_holder_name'), "card_holder_name")->attributes([]) !!}
			{!! html()->text("card_holder_name", $payment_line->card_holder_name)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.card_holder_name')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_transaction_no'), "card_transaction_number")->attributes([]) !!}
			{!! html()->text("card_transaction_number", $payment_line->card_transaction_number)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.card_transaction_no')]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-3">
		<div class="form-group">
			{!! html()->label(__('lang_v1.card_type'), "card_type")->attributes([]) !!}
			{!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })("card_type", ['credit' => 'Credit Card', 'debit' => 'Debit Card', 'visa' => 'Visa', 'master' => 'MasterCard'], $payment_line->card_type, ['class' => 'form-control select2'])); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! html()->label(__('lang_v1.month'), "card_month")->attributes([]) !!}
			{!! html()->text("card_month", $payment_line->card_month)->attributes(['class' => 'form-control', 
			'placeholder' => __('lang_v1.month') ]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! html()->label(__('lang_v1.year'), "card_year")->attributes([]) !!}
			{!! html()->text("card_year", $payment_line->card_year)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.year') ]); !!}
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			{!! html()->label(__('lang_v1.security_code'), "card_security")->attributes([]) !!}
			{!! html()->text("card_security", $payment_line->card_security)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.security_code')]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="payment_details_div @if( $payment_line->method !== 'cheque' ) {{ 'hide' }} @endif" data-type="cheque" >
	<div class="col-md-12">
		<div class="form-group">
			{!! html()->label(__('lang_v1.cheque_no'), "cheque_number")->attributes([]) !!}
			{!! html()->text("cheque_number", $payment_line->cheque_number)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.cheque_no')]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line->method !== 'bank_transfer' ) {{ 'hide' }} @endif" data-type="bank_transfer" >
	<div class="col-md-12">
		<div class="form-group">
			{!! html()->label(__('lang_v1.bank_account_number'), "bank_account_number")->attributes([]) !!}
			{!! html()->text("bank_account_number", $payment_line->bank_account_number)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.bank_account_number')]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line->method !== 'custom_pay_1' ) {{ 'hide' }} @endif" data-type="custom_pay_1" >
	<div class="col-md-12">
		<div class="form-group">
			{!! html()->label(__('lang_v1.transaction_no'), "transaction_no_1")->attributes([]) !!}
			{!! html()->text("transaction_no_1", $payment_line->transaction_no)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no')]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line->method !== 'custom_pay_2' ) {{ 'hide' }} @endif" data-type="custom_pay_2" >
	<div class="col-md-12">
		<div class="form-group">
			{!! html()->label(__('lang_v1.transaction_no'), "transaction_no_2")->attributes([]) !!}
			{!! html()->text("transaction_no_2", $payment_line->transaction_no)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no')]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line->method !== 'custom_pay_3' ) {{ 'hide' }} @endif" data-type="custom_pay_3" >
	<div class="col-md-12">
		<div class="form-group">
			{!! html()->label(__('lang_v1.transaction_no'), "transaction_no_3")->attributes([]) !!}
			{!! html()->text("transaction_no_3", $payment_line->transaction_no)->attributes(['class' => 'form-control', 'placeholder' => __('lang_v1.transaction_no')]); !!}
		</div>
	</div>
</div>