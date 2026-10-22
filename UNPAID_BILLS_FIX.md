# Unpaid Bills Total Due Amount Fix

## Problem
When users clicked the "Clear All Bills" button, a confirmation dialog appeared but showed "undefined" for the **Total Due Amount**:

```
Total Unpaid Invoices: 2
Total Due Amount: undefined
```

This was a poor user experience and didn't provide necessary information for confirmation.

## Root Cause
The frontend JavaScript in [clear_bills_javascript.blade.php](resources/views/sale_pos/partials/clear_bills_javascript.blade.php) expected the API response to include a `total_due` field:

```javascript
"{{ __('lang_v1.total_due_amount') }}: " + response.total_due,
```

However, the backend method `checkUnpaidBills()` in [SellController.php](app/Http/Controllers/SellController.php) was only returning:
- `count`: number of unpaid invoices
- `has_unpaid`: boolean flag

It was **not calculating or returning `total_due`**, causing the JavaScript to receive `undefined`.

## Solution
Updated the `checkUnpaidBills()` method to:

1. **Fetch all unpaid invoices** with their payment_lines relationship
2. **Calculate the total due amount** by iterating through invoices:
   - For each invoice: `due_amount = final_total - sum(payment_lines.amount)`
   - Sum all due amounts to get `total_due`
3. **Format the amount** using `transactionUtil->num_f()` with currency formatting
4. **Return the formatted total** in the JSON response

### Code Change
**File**: [app/Http/Controllers/SellController.php](app/Http/Controllers/SellController.php) (lines 1441-1469)

**Before**:
```php
public function checkUnpaidBills()
{
    $business_id = request()->session()->get('user.business_id');
    
    $count = Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where(function($query) {
                        $query->where('payment_status', 'due')
                              ->orWhere('payment_status', 'partial')
                              ->orWhereNull('payment_status');
                    })
                    ->where('status', 'final')
                    ->count();

    return response()->json([
        'count' => $count,
        'has_unpaid' => $count > 0
    ]);
}
```

**After**:
```php
public function checkUnpaidBills()
{
    $business_id = request()->session()->get('user.business_id');
    
    $unpaidInvoices = Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where(function($query) {
                        $query->where('payment_status', 'due')
                              ->orWhere('payment_status', 'partial')
                              ->orWhereNull('payment_status');
                    })
                    ->where('status', 'final')
                    ->with(['payment_lines'])
                    ->get();
    
    $total_due = 0;
    foreach ($unpaidInvoices as $invoice) {
        $due_amount = $invoice->final_total - $invoice->payment_lines->sum('amount');
        if ($due_amount > 0) {
            $total_due += $due_amount;
        }
    }

    return response()->json([
        'count' => $unpaidInvoices->count(),
        'has_unpaid' => $unpaidInvoices->count() > 0,
        'total_due' => $this->transactionUtil->num_f($total_due, true)
    ]);
}
```

## Changes
- Added eager loading of `payment_lines` relationship
- Calculate total due by subtracting paid amounts from final totals
- Format the total using the existing transaction utility
- Return `total_due` in the JSON response with proper currency formatting

## Impact
✅ **Frontend**: Confirmation dialog now displays the correct formatted total due amount
✅ **UX**: Users can see exact amount before confirming mass bill clearance
✅ **Backend**: No breaking changes; existing `count` and `has_unpaid` fields still returned
✅ **Performance**: Single query with eager loading; no N+1 queries

## Testing
1. Navigate to the sales/bills section
2. Click "Clear All Bills" button
3. Verify confirmation dialog shows:
   - Correct count of unpaid invoices
   - **Correct total due amount (not "undefined")**
   - Currency formatting applied

## Related Files
- [clear_bills_javascript.blade.php](resources/views/sale_pos/partials/clear_bills_javascript.blade.php) - Uses `response.total_due` on line 30
- [SellController.php](app/Http/Controllers/SellController.php) - `checkUnpaidBills()` and `clearAllBills()` methods
- [routes/web.php](routes/web.php) - Route definition for `sells.check-unpaid-bills`
