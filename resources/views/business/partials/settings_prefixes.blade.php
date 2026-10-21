<div class="pos-tab-content">
     <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_prefix = '';
                    if(!empty($business->ref_no_prefixes['purchase'])){
                        $purchase_prefix = $business->ref_no_prefixes['purchase'];
                    }
                @endphp
                {!! html()->label(__('lang_v1.purchase') . ':', 'ref_no_prefixes[purchase]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[purchase]', $purchase_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_return = '';
                    if(!empty($business->ref_no_prefixes['purchase_return'])){
                        $purchase_return = $business->ref_no_prefixes['purchase_return'];
                    }
                @endphp
                {!! html()->label(__('lang_v1.purchase_return') . ':', 'ref_no_prefixes[purchase_return]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[purchase_return]', $purchase_return)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_requisition_prefix = !empty($business->ref_no_prefixes['purchase_requisition']) ? $business->ref_no_prefixes['purchase_requisition'] : '';
                @endphp
                {!! html()->label(__('lang_v1.purchase_requisition') . ':', 'ref_no_prefixes[purchase_requisition]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[purchase_requisition]', $purchase_requisition_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_order_prefix = !empty($business->ref_no_prefixes['purchase_order']) ? $business->ref_no_prefixes['purchase_order'] : '';
                @endphp
                {!! html()->label(__('lang_v1.purchase_order') . ':', 'ref_no_prefixes[purchase_order]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[purchase_order]', $purchase_order_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $stock_transfer_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_transfer'])){
                        $stock_transfer_prefix = $business->ref_no_prefixes['stock_transfer'];
                    }
                @endphp
                {!! html()->label(__('lang_v1.stock_transfer') . ':', 'ref_no_prefixes[stock_transfer]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[stock_transfer]', $stock_transfer_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $stock_adjustment_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_adjustment'])){
                        $stock_adjustment_prefix = $business->ref_no_prefixes['stock_adjustment'];
                    }
                @endphp
                {!! html()->label(__('stock_adjustment.stock_adjustment') . ':', 'ref_no_prefixes[stock_adjustment]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[stock_adjustment]', $stock_adjustment_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sell_return_prefix = '';
                    if(!empty($business->ref_no_prefixes['sell_return'])){
                        $sell_return_prefix = $business->ref_no_prefixes['sell_return'];
                    }
                @endphp
                {!! html()->label(__('lang_v1.sell_return') . ':', 'ref_no_prefixes[sell_return]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[sell_return]', $sell_return_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expenses_prefix = '';
                    if(!empty($business->ref_no_prefixes['expense'])){
                        $expenses_prefix = $business->ref_no_prefixes['expense'];
                    }
                @endphp
                {!! html()->label(__('expense.expenses') . ':', 'ref_no_prefixes[expense]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[expense]', $expenses_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $contacts_prefix = '';
                    if(!empty($business->ref_no_prefixes['contacts'])){
                        $contacts_prefix = $business->ref_no_prefixes['contacts'];
                    }
                @endphp
                {!! html()->label(__('contact.contacts') . ':', 'ref_no_prefixes[contacts]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[contacts]', $contacts_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_payment = '';
                    if(!empty($business->ref_no_prefixes['purchase_payment'])){
                        $purchase_payment = $business->ref_no_prefixes['purchase_payment'];
                    }
                @endphp
                {!! html()->label(__('lang_v1.purchase_payment') . ':', 'ref_no_prefixes[purchase_payment]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[purchase_payment]', $purchase_payment)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sell_payment = '';
                    if(!empty($business->ref_no_prefixes['sell_payment'])){
                        $sell_payment = $business->ref_no_prefixes['sell_payment'];
                    }
                @endphp
                {!! html()->label(__('lang_v1.sell_payment') . ':', 'ref_no_prefixes[sell_payment]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[sell_payment]', $sell_payment)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expense_payment = '';
                    if(!empty($business->ref_no_prefixes['expense_payment'])){
                        $expense_payment = $business->ref_no_prefixes['expense_payment'];
                    }
                @endphp
                {!! html()->label(__('lang_v1.expense_payment') . ':', 'ref_no_prefixes[expense_payment]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[expense_payment]', $expense_payment)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $business_location_prefix = '';
                    if(!empty($business->ref_no_prefixes['business_location'])){
                        $business_location_prefix = $business->ref_no_prefixes['business_location'];
                    }
                @endphp
                {!! html()->label(__('business.business_location') . ':', 'ref_no_prefixes[business_location]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[business_location]', $business_location_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $username_prefix = !empty($business->ref_no_prefixes['username']) ? $business->ref_no_prefixes['username'] : '';
                @endphp
                {!! html()->label(__('business.username') . ':', 'ref_no_prefixes[username]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[username]', $username_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $subscription_prefix = !empty($business->ref_no_prefixes['subscription']) ? $business->ref_no_prefixes['subscription'] : '';
                @endphp
                {!! html()->label(__('lang_v1.subscription_no') . ':', 'ref_no_prefixes[subscription]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[subscription]', $subscription_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['draft']) ? $business->ref_no_prefixes['draft'] : '';
                @endphp
                {!! html()->label(__('sale.draft') . ':', 'ref_no_prefixes[draft]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[draft]', $draft_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sales_order_prefix = !empty($business->ref_no_prefixes['sales_order']) ? $business->ref_no_prefixes['sales_order'] : '';
                @endphp
                {!! html()->label(__('lang_v1.sales_order') . ':', 'ref_no_prefixes[sales_order]')->attributes([]) !!}
                {!! html()->text('ref_no_prefixes[sales_order]', $sales_order_prefix)->attributes(['class' => 'form-control']); !!}
            </div>
        </div>
    </div>
</div>