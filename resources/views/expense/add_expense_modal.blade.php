<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! ((function ($options) { $method = $options['method'] ?? 'POST'; if (isset($options['url'])) { $url = $options['url']; $action = is_array($url) ? url($url[0], array_slice($url, 1)) : url($url); } elseif (isset($options['route'])) { $route = $options['route']; if (is_array($route)) { $parameters = array_slice($route, 1); if (array_keys($route) === [0, 1]) { $parameters = $parameters[0]; } $action = route($route[0], $parameters); } else { $action = route($route); } } elseif (isset($options['action'])) { $action = action($options['action']); } else { $action = url()->current(); } $attributes = array_merge(['accept-charset' => 'UTF-8'], \Illuminate\Support\Arr::except($options, ['method', 'url', 'route', 'action', 'files'])); $form = html()->form($method, $action); if (!empty($options['files'])) { $form = $form->acceptsFiles(); } return $form->attributes($attributes); })(['url' => action([\App\Http\Controllers\ExpenseController::class, 'store']), 'method' => 'post', 'id' => 'add_expense_modal_form', 'files' => true ]))->open() !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'expense.add_expense' )</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                @if(count($business_locations) == 1)
                    @php 
                        $default_location = current(array_keys($business_locations->toArray())) 
                    @endphp
                @else
                    @php $default_location = request()->input('location_id'); @endphp
                @endif
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('purchase.business_location').':*', 'expense_location_id')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'expense_location_id'])); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('expense.expense_category').':', 'expense_category_id')->attributes([]) !!}
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('expense_category_id', $expense_categories, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
                    </div>
                </div>
                <div class="col-md-6">
					<div class="form-group">
			            {!! html()->label(__('product.sub_category') . ':', 'expense_sub_category_id')->attributes([]) !!}
			              {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('expense_sub_category_id', [], null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'])); !!}
			          </div>
				</div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('purchase.ref_no').':', 'expense_ref_no')->attributes([]) !!}
                        {!! html()->text('ref_no', null)->attributes(['class' => 'form-control', 'id' => 'expense_ref_no']); !!}
                        <p class="help-block">
                            @lang('lang_v1.leave_empty_to_autogenerate')
                        </p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('messages.date') . ':*', 'expense_transaction_date')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! html()->text('transaction_date', @format_datetime('now'))->attributes(['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('expense.expense_for').':', 'expense_for')->attributes([]) !!} @show_tooltip(__('tooltip.expense_for'))
                        {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('expense_for', $users, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')])); !!}
                    </div>
                </div>                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! html()->label(__('product.applicable_tax') . ':', 'expense_tax_id')->attributes([]) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! ((function ($name, $options, $value, $attributes) { $selected = is_array($value) ? array_map('strval', $value) : ($value === null ? null : (string) $value); $element = html()->select($name, $options, $selected)->attributes(\Illuminate\Support\Arr::except($attributes ?? [], ['placeholder'])); return array_key_exists('placeholder', $attributes ?? []) ? $element->placeholder($attributes['placeholder']) : $element; })('tax_id', $taxes['tax_rates'], null, ['class' => 'form-control', 'id'=>'expense_tax_id'])); !!}

                            <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
                            value="0">
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('sale.total_amount') . ':*', 'expense_final_total')->attributes([]) !!}
                        {!! html()->text('final_total', null)->attributes(['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required', 'id' => 'expense_final_total']); !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! html()->label(__('expense.expense_note') . ':', 'expense_additional_notes')->attributes([]) !!}
                                {!! html()->textarea('additional_notes', null)->attributes(['class' => 'form-control', 'rows' => 3, 'id' => 'expense_additional_notes']); !!}
                    </div>
                </div>
            </div>

            <div class="payment_row">
                <h4>@lang('purchase.add_payment'):</h4>
                @include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true])
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right">
                            <strong>@lang('purchase.payment_due'):</strong>
                            <span id="expense_payment_due">{{@num_format(0)}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! html()->form()->close() !!}
    </div>
</div>
