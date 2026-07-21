@extends('layouts.app')

@php
    $title = __('lang_v1.split_bill');
@endphp

@section('title', $title)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ $title }}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            <!-- Transaction Info Box -->
            @component('components.widget', ['class' => 'box-solid'])
                <div class="row">
                    <div class="col-md-6">
                        <strong>@lang('sale.invoice_no'):</strong> {{ $sell->invoice_no }}
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('sale.date'):</strong> {{ @format_datetime($sell->transaction_date) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>@lang('contact.customer'):</strong> {{ $sell->contact->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('business.location'):</strong> {{ $sell->location->name }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>@lang('sale.final_total'):</strong> @format_currency($sell->final_total)
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('sale.payment_status'):</strong> 
                        <span class="label {{ $sell->payment_status == 'paid' ? 'bg-green' : ($sell->payment_status == 'partial' ? 'bg-yellow' : 'bg-red') }}">
                            {{ __('lang_v1.' . $sell->payment_status) }}
                        </span>
                    </div>
                </div>
            @endcomponent

            <!-- Products Table Box -->
            @component('components.widget', ['class' => 'box-solid', 'title' => __('sale.select_products_to_split')])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="split-bill-table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>@lang('product.product_name')</th>
                                <th>@lang('lang_v1.variation')</th>
                                <th class="text-center">@lang('sale.qty')</th>
                                <th class="text-right">@lang('sale.price')</th>
                                <th class="text-right">@lang('sale.subtotal')</th>
                                <th class="text-center">
                                    <input type="checkbox" id="select-all-lines">
                                    <label for="select-all-lines" class="ml-5">@lang('messages.select_all')</label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sell->sell_lines as $index => $line)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $line->product->name }}</td>
                                <td>
                                    @if(!empty($line->variations->name) && $line->variations->name != 'DUMMY')
                                        {{ $line->variations->name }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="input-group input-group-sm" style="width: 100px; margin: 0 auto;">
                                        <input type="number" class="form-control input-sm split-quantity" 
                                            value="{{ $line->quantity }}" 
                                            min="1" max="{{ $line->quantity }}"
                                            data-line-id="{{ $line->id }}"
                                            data-max-qty="{{ $line->quantity }}">
                                        <span class="input-group-addon">/ {{ @format_quantity($line->quantity) }}</span>
                                    </div>
                                </td>
                                <td class="text-right">@format_currency($line->unit_price)</td>
                                <td class="text-right">@format_currency($line->unit_price * $line->quantity)</td>
                                <td class="text-center">
                                    <input type="checkbox" class="split-line-checkbox" 
                                           value="{{ $line->id }}" 
                                           data-price="{{ $line->unit_price }}"
                                           data-quantity="{{ $line->quantity }}"
                                           data-total="{{ $line->unit_price * $line->quantity }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">
                                    <strong>@lang('sale.selected_total')</strong>
                                </th>
                                <th id="selected-total" class="text-right">@format_currency(0)</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endcomponent

            <!-- Split Form Box -->
            @component('components.widget', ['class' => 'box-solid', 'title' => __('lang_v1.split_bill_details')])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="split_customer">@lang('sale.customer') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <select class="form-control" id="split_customer" required>
                                    <option value="">@lang('messages.please_select')</option>
                                    @foreach($customers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="split_location">@lang('business.location') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                <select class="form-control" id="split_location" required>
                                    <option value="">@lang('messages.please_select')</option>
                                    @foreach($business_locations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('lang_v1.payment_method')</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-money-bill-alt"></i>
                                </span>
                                <select class="form-control" id="split_payment_method">
                                    <option value="">@lang('messages.please_select')</option>
                                    @foreach($payment_types as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('sale.payment_status')</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                <select class="form-control" id="split_payment_status">
                                    <option value="due">@lang('lang_v1.due')</option>
                                    <option value="paid">@lang('lang_v1.paid')</option>
                                    <option value="partial">@lang('lang_v1.partial')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('sale.status')</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                <select class="form-control" id="split_status">
                                    <option value="final">@lang('sale.final')</option>
                                    <option value="draft">@lang('sale.draft')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('sale.additional_notes')</label>
                            <textarea class="form-control" id="split_notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-md-offset-9">
                        <div class="form-group">
                            <label class="text-primary">@lang('sale.split_amount')</label>
                            <h3 id="split-amount-display" class="text-primary">@format_currency(0)</h3>
                            <input type="hidden" id="split-amount" value="0">
                        </div>
                    </div>
                </div>
            @endcomponent

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-sm-12 text-center tw-mt-4">
                    <button type="button" class="tw-dw-btn tw-dw-btn-default tw-dw-btn-lg btn-close-modal" data-dismiss="modal">
                        <i class="fa fa-times"></i> @lang('messages.cancel')
                    </button>
                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white" id="confirm-split-btn">
                        <i class="fa fa-cut"></i> @lang('lang_v1.split_bill')
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Store translation strings in JavaScript variables
        var langQuantityExceedsAvailable = '{{ __("lang_v1.quantity_exceeds_available", ["available" => ""]) }}';
        var langQuantityMustBeGreaterThanZero = '{{ __("lang_v1.quantity_must_be_greater_than_zero") }}';
        var langSelectAtLeastOneProduct = '{{ __("lang_v1.select_at_least_one_product") }}';
        var langPleaseSelectCustomer = '{{ __("sale.please_select_customer") }}';
        var langPleaseSelectLocation = '{{ __("sale.please_select_location") }}';
        var langProcessing = '{{ __("messages.processing") }}';
        var langSomethingWentWrong = '{{ __("messages.something_went_wrong") }}';
        var langCannotSplitAllItems = '{{ __("lang_v1.cannot_split_all_items") }}';

        // Select all checkbox
        $('#select-all-lines').change(function() {
            $('.split-line-checkbox').prop('checked', $(this).prop('checked'));
            calculateTotal();
        });

        // Calculate total on checkbox change
        $('.split-line-checkbox').change(function() {
            calculateTotal();
        });

        // Calculate total on quantity change
        $(document).on('change', '.split-quantity', function() {
            var maxQty = parseFloat($(this).data('max-qty'));
            var val = parseFloat($(this).val()) || 0;
            
            if (val < 1) {
                $(this).val(1);
            } else if (val > maxQty) {
                $(this).val(maxQty);
                toastr.warning(langQuantityExceedsAvailable.replace(':available', maxQty));
            }
            
            calculateTotal();
        });

        // Calculate selected total with quantities
        function calculateTotal() {
            var total = 0;
            $('.split-line-checkbox:checked').each(function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat(row.find('.split-quantity').val()) || 0;
                var price = parseFloat($(this).data('price'));
                total += price * quantity;
            });
            $('#selected-total').text('{{ $sell->location->currency->symbol ?? 'K' }}' + total.toFixed(2));
            $('#split-amount-display').text('{{ $sell->location->currency->symbol ?? 'K' }}' + total.toFixed(2));
            $('#split-amount').val(total);
        }

        // Confirm split
        $('#confirm-split-btn').click(function() {
            var selectedLines = [];
            var hasInvalidQuantity = false;
            var totalRemainingItems = 0;
            
            // Check all lines for remaining items
            $('.split-line-checkbox').each(function() {
                var row = $(this).closest('tr');
                var maxQty = parseFloat(row.find('.split-quantity').data('max-qty'));
                
                if ($(this).is(':checked')) {
                    var quantity = parseFloat(row.find('.split-quantity').val()) || 0;
                    var lineId = $(this).val();
                    
                    if (quantity <= 0) {
                        toastr.error(langQuantityMustBeGreaterThanZero);
                        hasInvalidQuantity = true;
                        return false;
                    }
                    
                    if (quantity > maxQty) {
                        toastr.error(langQuantityExceedsAvailable.replace(':available', maxQty));
                        hasInvalidQuantity = true;
                        return false;
                    }
                    
                    selectedLines.push({
                        id: lineId,
                        quantity: quantity
                    });
                    
                    // Calculate remaining items for selected lines
                    totalRemainingItems += (maxQty - quantity);
                } else {
                    // Items not selected remain
                    totalRemainingItems += maxQty;
                }
            });

            if (hasInvalidQuantity) {
                return false;
            }

            // Check if all items are being moved (no items remaining)
            if (totalRemainingItems === 0) {
                toastr.error(langCannotSplitAllItems);
                return false;
            }

            if (selectedLines.length === 0) {
                toastr.error(langSelectAtLeastOneProduct);
                return false;
            }

            var customer = $('#split_customer').val();
            var location = $('#split_location').val();

            if (!customer) {
                toastr.error(langPleaseSelectCustomer);
                return false;
            }

            if (!location) {
                toastr.error(langPleaseSelectLocation);
                return false;
            }

            var data = {
                transaction_id: {{ $sell->id }},
                sell_lines: selectedLines,
                customer_id: customer,
                location_id: location,
                payment_method: $('#split_payment_method').val(),
                payment_status: $('#split_payment_status').val(),
                status: $('#split_status').val(),
                notes: $('#split_notes').val(),
                _token: '{{ csrf_token() }}'
            };

            $('#confirm-split-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + langProcessing);

            $.ajax({
                url: '{{ action([\App\Http\Controllers\SellController::class, 'processSplitBill']) }}',
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        setTimeout(function() {
                            // Close modal and redirect
                            $('.btn-close-modal').click();
                            setTimeout(function() {
                                window.location.href = '{{ action([\App\Http\Controllers\SellController::class, "index"]) }}';
                            }, 300);
                        }, 1500);
                    } else {
                        toastr.error(response.msg);
                        $('#confirm-split-btn').prop('disabled', false).html('<i class="fa fa-cut"></i> @lang("lang_v1.split_bill")');
                    }
                },
                error: function(xhr) {
                    var errorMsg = langSomethingWentWrong;
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    }
                    toastr.error(errorMsg);
                    console.error(xhr.responseText);
                    $('#confirm-split-btn').prop('disabled', false).html('<i class="fa fa-cut"></i> @lang("lang_v1.split_bill")');
                }
            });
        });

        // Initialize calculateTotal on page load
        calculateTotal();
    });
</script>
@endsection