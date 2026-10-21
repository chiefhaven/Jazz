<!-- Edit Shipping Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="posShippingModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang('sale.shipping')</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
				        <div class="form-group">
				            {!! html()->label(__('sale.shipping_details') . ':*', 'shipping_details_modal')->attributes([]) !!}
				            {!! html()->textarea('shipping_details_modal', !empty($transaction->shipping_details) ? $transaction->shipping_details : '')->attributes(['class' => 'form-control','placeholder' => __('sale.shipping_details'), 'required' ,'rows' => '4']); !!}
				        </div>
				    </div>

				    <div class="col-md-6">
				        <div class="form-group">
				            {!! html()->label(__('lang_v1.shipping_address') . ':', 'shipping_address_modal')->attributes([]) !!}
				            {!! html()->textarea('shipping_address_modal', !empty($transaction->shipping_address) ? $transaction->shipping_address : '')->attributes(['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '4']); !!}
				        </div>
				    </div>

				    <div class="col-md-6">
				        <div class="form-group">
				            {!! html()->label(__('sale.shipping_charges') . ':*', 'shipping_charges_modal')->attributes([]) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-info"></i>
				                </span>
				                {!! html()->text('shipping_charges_modal', !empty($transaction->shipping_charges) ? @num_format($transaction->shipping_charges) : 0)->attributes(['class' => 'form-control input_number input_number', 'data-decimal' => 'no_neg', 'placeholder' => __('sale.shipping_charges')]); !!}
				            </div>
				        </div>
				    </div>

				    <div class="col-md-6">
				        <div class="form-group">
				            {!! html()->label(__('lang_v1.shipping_status') . ':', 'shipping_status_modal')->attributes([]) !!}
				            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('shipping_status_modal', $shipping_statuses, !empty($transaction->shipping_status) ? $transaction->shipping_status : null, ['class' => 'form-control','placeholder' => __('messages.please_select')])); !!}
				        </div>
				    </div>

				    <div class="col-md-6">
				        <div class="form-group">
				            {!! html()->label(__('lang_v1.delivered_to') . ':', 'delivered_to_modal')->attributes([]) !!}
				            {!! html()->text('delivered_to_modal', !empty($transaction->delivered_to) ? $transaction->delivered_to : null)->attributes(['class' => 'form-control','placeholder' => __('lang_v1.delivered_to')]); !!}
				        </div>
				    </div>
					<div class="col-md-6">
				        <div class="form-group">
				            {!! html()->label(__('lang_v1.delivery_person') . ':', 'delivery_person_modal')->attributes([]) !!} <br>
				            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('delivery_person_modal', $users, !empty($transaction->delivery_person) ? $transaction->delivery_person : null, ['style' => 'width:100%' ,'class' => 'form-control select2 width-100','placeholder' => __('messages.please_select')])); !!}
				        </div>
				    </div>
				    @php
				        $custom_labels = json_decode(session('business.custom_labels'), true);

				        $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

				        $is_shipping_custom_field_1_required = !empty($custom_labels['shipping']['is_custom_field_1_required']) && $custom_labels['shipping']['is_custom_field_1_required'] == 1 ? true : false;

				        $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

				        $is_shipping_custom_field_2_required = !empty($custom_labels['shipping']['is_custom_field_2_required']) && $custom_labels['shipping']['is_custom_field_2_required'] == 1 ? true : false;

				        $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';
				        
				        $is_shipping_custom_field_3_required = !empty($custom_labels['shipping']['is_custom_field_3_required']) && $custom_labels['shipping']['is_custom_field_3_required'] == 1 ? true : false;

				        $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';
				        
				        $is_shipping_custom_field_4_required = !empty($custom_labels['shipping']['is_custom_field_4_required']) && $custom_labels['shipping']['is_custom_field_4_required'] == 1 ? true : false;

				        $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
				        
				        $is_shipping_custom_field_5_required = !empty($custom_labels['shipping']['is_custom_field_5_required']) && $custom_labels['shipping']['is_custom_field_5_required'] == 1 ? true : false;
			        @endphp

			        @if(!empty($shipping_custom_label_1))
			        	@php
			        		$label_1 = $shipping_custom_label_1 . ':';
			        		if($is_shipping_custom_field_1_required) {
			        			$label_1 .= '*';
			        		}
			        	@endphp

			        	<div class="col-md-6">
					        <div class="form-group">
					            {!! html()->label($label_1, 'shipping_custom_field_1')->attributes([]) !!}
					            {!! html()->text('shipping_custom_field_1', !empty($transaction->shipping_custom_field_1) ? $transaction->shipping_custom_field_1 : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
					        </div>
					    </div>
			        @endif
			        @if(!empty($shipping_custom_label_2))
			        	@php
			        		$label_2 = $shipping_custom_label_2 . ':';
			        		if($is_shipping_custom_field_2_required) {
			        			$label_2 .= '*';
			        		}
			        	@endphp

			        	<div class="col-md-6">
					        <div class="form-group">
					            {!! html()->label($label_2, 'shipping_custom_field_2')->attributes([]) !!}
					            {!! html()->text('shipping_custom_field_2', !empty($transaction->shipping_custom_field_2) ? $transaction->shipping_custom_field_2 : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
					        </div>
					    </div>
			        @endif
			        @if(!empty($shipping_custom_label_3))
			        	@php
			        		$label_3 = $shipping_custom_label_3 . ':';
			        		if($is_shipping_custom_field_3_required) {
			        			$label_3 .= '*';
			        		}
			        	@endphp

			        	<div class="col-md-6">
					        <div class="form-group">
					            {!! html()->label($label_3, 'shipping_custom_field_3')->attributes([]) !!}
					            {!! html()->text('shipping_custom_field_3', !empty($transaction->shipping_custom_field_3) ? $transaction->shipping_custom_field_3 : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
					        </div>
					    </div>
			        @endif
			        @if(!empty($shipping_custom_label_4))
			        	@php
			        		$label_4 = $shipping_custom_label_4 . ':';
			        		if($is_shipping_custom_field_4_required) {
			        			$label_4 .= '*';
			        		}
			        	@endphp

			        	<div class="col-md-6">
					        <div class="form-group">
					            {!! html()->label($label_4, 'shipping_custom_field_4')->attributes([]) !!}
					            {!! html()->text('shipping_custom_field_4', !empty($transaction->shipping_custom_field_4) ? $transaction->shipping_custom_field_4 : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
					        </div>
					    </div>
			        @endif
			        @if(!empty($shipping_custom_label_5))
			        	@php
			        		$label_5 = $shipping_custom_label_5 . ':';
			        		if($is_shipping_custom_field_5_required) {
			        			$label_5 .= '*';
			        		}
			        	@endphp

			        	<div class="col-md-6">
					        <div class="form-group">
					            {!! html()->label($label_5, 'shipping_custom_field_5')->attributes([]) !!}
					            {!! html()->text('shipping_custom_field_5', !empty($transaction->shipping_custom_field_5) ? $transaction->shipping_custom_field_5 : null)->attributes(['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
					        </div>
					    </div>
			        @endif
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="posShippingModalUpdate">@lang('messages.update')</button>
			    <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.cancel')</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

