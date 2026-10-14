@if(!empty($receipt_details->eis_invoice_number))
	<br/>
	<span style="word-break: break-all; overflow-wrap: anywhere;">
		<strong>{{ __('sale.transaction_number') }}:</strong> {{ $receipt_details->transaction_number ?? $receipt_details->invoice_no }}
	</span>
	<br/>
	<span style="word-break: break-all; overflow-wrap: anywhere;">
		<strong>{{ __('sale.eis_invoice_number') }}:</strong> {{ $receipt_details->eis_invoice_number }}
	</span>
@endif