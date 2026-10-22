<?php

require __DIR__.'/saved_sale_split_checks.php';

use App\Services\Sales\RestaurantBillService;
use Illuminate\Database\Schema\Blueprint;

class IsolatedRestaurantService extends RestaurantBillService
{
    public int $scheduled = 0;
    protected function checkFiscalLimits(array $bill): void {}
    protected function scheduleFiscal(int $id): void
    {
        Illuminate\Database\Capsule\Manager::connection()->afterCommit(function () { ++$this->scheduled; });
    }
}

function restaurantFixture(): array
{
    [$db, $unused, $actor] = checkoutFixture(true);
    $schema = $db->getSchemaBuilder();
    Illuminate\Container\Container::getInstance()->instance('translator',
        new Illuminate\Translation\Translator(new Illuminate\Translation\ArrayLoader(), 'en'));
    $schema->table('transactions', function (Blueprint $t) {
        foreach (['res_table_id', 'res_waiter_id', 'is_kitchen_order', 'res_order_status'] as $c) { $t->text($c)->nullable(); }
    });
    $schema->table('business', function (Blueprint $t) { $t->text('enabled_modules')->nullable(); $t->text('custom_labels')->nullable(); });
    $schema->table('contacts', function (Blueprint $t) { $t->boolean('is_default')->default(0); });
    $schema->table('products', function (Blueprint $t) { $t->text('name')->default('Test product'); });
    $schema->table('variations', function (Blueprint $t) { $t->text('name')->default('DUMMY'); });
    $schema->create('cash_registers', function (Blueprint $t) {
        $t->increments('id'); $t->integer('user_id'); $t->integer('business_id'); $t->integer('location_id'); $t->text('status');
    });
    $schema->table('cash_register_transactions', function (Blueprint $t) {
        foreach (['cash_register_id', 'amount', 'pay_method', 'type', 'transaction_type'] as $column) { $t->text($column)->nullable(); }
    });
    $schema->create('res_tables', function (Blueprint $t) {
        $t->increments('id'); $t->integer('business_id'); $t->integer('location_id'); $t->text('name'); $t->text('deleted_at')->nullable();
    });
    foreach ([
        'restaurant_bills' => ['transaction_id', 'business_id', 'location_id', 'table_id', 'waiter_id', 'state', 'version'],
        'restaurant_tickets' => ['transaction_id', 'variation_id', 'station', 'quantity', 'original_quantity', 'status', 'created_by', 'completed_by'],
        'restaurant_approvals' => ['transaction_id', 'requested_by', 'approved_by', 'expected_version', 'items_json', 'reason', 'disposition', 'status'],
        'restaurant_operations' => ['business_id', 'transaction_id', 'user_id', 'operation_id', 'payload_hash', 'response_json'],
        'restaurant_waste' => ['approval_id', 'transaction_id', 'variation_id', 'purchase_line_id', 'quantity'],
    ] as $name => $columns) {
        $schema->create($name, function (Blueprint $t) use ($name, $columns) {
            $t->increments('id');
            foreach (array_merge($columns, ['created_at', 'updated_at']) as $column) { $t->text($column)->nullable(); }
            if ($name === 'restaurant_operations') { $t->unique(['business_id', 'operation_id']); }
            if ($name === 'restaurant_bills') { $t->unique('transaction_id'); }
        });
    }
    $db->table('business')->update(['enabled_modules' => '["tables","kitchen","service_staff"]']);
    $db->table('contacts')->update(['is_default' => 1]);
    $db->table('res_tables')->insert(['id' => 1, 'business_id' => 1, 'location_id' => 2, 'name' => 'Table 1']);
    $db->table('restaurant_bills')->insert(['transaction_id' => 1, 'business_id' => 1, 'location_id' => 2,
        'table_id' => 1, 'waiter_id' => 7, 'state' => 'open', 'version' => 1]);
    $db->table('transactions')->where('id', 1)->update(['res_table_id' => 1, 'res_waiter_id' => 7]);
    $db->table('saved_sale_split_operations')->insert(['business_id' => 1, 'user_id' => 7,
        'operation_id' => (string) Illuminate\Support\Str::uuid(), 'source_id' => 1, 'result_id' => 1,
        'allocation_json' => '{"kind":"restaurant_open"}', 'response_json' => '{"id":1}']);
    return [$db, new IsolatedRestaurantService($db), $actor];
}

function restaurantInput($service, $actor, array $extra = [], int $id = 1): array
{
    $board = $service->board($actor, 2);
    $bill = collect($board['bills'])->firstWhere('id', $id);
    return ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'expected_version' => $bill['version']] + $extra;
}
[$db, $service, $waiter] = restaurantFixture();
$before = snapshot($db);
check(count($service->config($waiter, 2)['tables']) === 1 && snapshot($db) === $before, 'restaurant config tenant-scoped and read-only');
$request = restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]]);
$response = $service->dispatch($waiter, 1, $request);
check($db->table('restaurant_tickets')->value('quantity') === '6.0000', 'first round sends total quantity once');
$after = snapshot($db);
check($service->dispatch($waiter, 1, $request) === $response && snapshot($db) === $after, 'dispatch retry never duplicates preparation');
rejects(fn () => $service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]])), 'restaurant_workflow');
$preview = $service->preview($waiter, 1);
$edit = ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'expected_version' => $preview['expected_version'],
    'restaurant_version' => $preview['restaurant_version'], 'items' => [['variation_id' => 5, 'quantity' => '8.0000']]];
$service->edit($waiter, 1, $edit);
$service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'kitchen', 'variations' => [5]]));
check($db->table('restaurant_tickets')->orderBy('id', 'desc')->value('quantity') === '2.0000', 'second round dispatch only sends added quantity');
$waiter->permissions[] = 'restaurant.bar';
$queue = $service->queue($waiter, 2, 'bar');
check(count($queue) === 1 && $queue[0]['quantity'] === '6.0000', 'station queue contains only its own round');
$service->ticket($waiter, 1, ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'station' => 'bar', 'ticket_id' => 1, 'status' => 'cooked']);
check($db->table('restaurant_tickets')->where('id', 1)->value('status') === 'cooked', 'authorized preparation completion');
$service->transition($waiter, 1, restaurantInput($service, $waiter, ['action' => 'ready']));
rejects(fn () => $service->preview($waiter, 1), 'restaurant_workflow');
$pay = restaurantInput($service, $waiter, ['payments' => [['method' => 'cash', 'amount' => '1']]]);
rejects(fn () => $service->pay($waiter, 1, $pay), 'restaurant_workflow');
$cashier = clone $waiter;
$cashier->id = 8;
$cashier->permissions = ['sell.view', 'sell.payments', 'location.2'];
$db->table('business_locations')->update(['default_payment_accounts' => '{"cash":{"is_enabled":1},"card":{"is_enabled":1}}']);
$pay = restaurantInput($service, $cashier, ['payments' => [['method' => 'cash', 'amount' => '10.0000'], ['method' => 'card', 'amount' => '20.0000', 'reference' => 'TEST-ONLY']]]);
$stock = $db->table('variation_location_details')->value('qty_available');
$result = $service->pay($cashier, 1, $pay);
check($result['payment_status'] === 'partial' && $result['balance'] === '66.0000' && $service->scheduled === 0, 'mixed partial tender does not fiscalize bill');
check($db->table('transaction_payments')->count() === 2 && $db->table('variation_location_details')->value('qty_available') === $stock, 'clearing only appends payments, never deducts stock twice');
$after = snapshot($db);
check($service->pay($cashier, 1, $pay) === $result && snapshot($db) === $after, 'payment retry does not double-charge');
$overpay = restaurantInput($service, $cashier, ['payments' => [['method' => 'cash', 'amount' => '999']]]);
rejects(fn () => $service->pay($cashier, 1, $overpay), 'restaurant_workflow');
$final = restaurantInput($service, $cashier, ['payments' => [['method' => 'card', 'amount' => '66.0000']]]);
$service->pay($cashier, 1, $final);
check($db->table('transactions')->where('id', 1)->value('payment_status') === 'paid' &&
    $db->table('restaurant_bills')->value('state') === 'closed' && $service->scheduled === 1, 'last tender closes bill and schedules fiscal submission once');
check(App\Services\Sales\SavedSaleWriteGuard::fiscal($db, 1)->payment_status === 'paid', 'paid restaurant bill satisfies persisted fiscal payment guard');
$paidSnapshot = snapshot($db);
$service->retryFiscal($cashier, 1);
check(snapshot($db) === $paidSnapshot && $service->scheduled === 2, 'fiscal retry schedules processing after commit without rewriting payments or inventory');
$db->table('eis_sales')->insert(['transaction_id' => 1, 'business_id' => 1, 'status' => 'submitted']);
$service->retryFiscal($cashier, 1);
check($service->scheduled === 2, 'already submitted bill does not queue another manual fiscal attempt');

foreach (['return_stock', 'waste'] as $disposition) {
    [$db, $service, $waiter] = restaurantFixture();
    $service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]]));
    $preview = $service->preview($waiter, 1);
    $input = ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'expected_version' => $preview['expected_version'],
        'restaurant_version' => $preview['restaurant_version'], 'items' => [['variation_id' => 5, 'quantity' => '1.0000']],
        'reason' => 'Customer cancelled test round', 'disposition' => $disposition];
    $stock = $db->table('variation_location_details')->value('qty_available');
    $request = $service->edit($waiter, 1, $input);
    check($request['approval_required'] && $db->table('transaction_sell_lines')->where('id', 1)->value('quantity') === '4.0000',
        'waiter reduction is pending, not applied without manager');
    $decision = ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'approval_id' => $request['approval_id'], 'decision' => 'approve'];
    rejects(fn () => $service->approve($waiter, 1, $decision), 'restaurant_workflow');
    $manager = clone $waiter; $manager->id = 8; $manager->permissions[] = 'restaurant.approve_void';
    $pending = $service->approvals($manager, 2);
    check(count($pending) === 1 && $pending[0]['invoice_no'] !== '' && $pending[0]['table_name'] === 'Table 1' &&
        str_contains($pending[0]['change_summary'], 'Test product: 6.0000 -> 1.0000') && !$pending[0]['is_stale'],
        'manager review identifies table/invoice/product, old/new quantities and dispatched quantity');
    $service->approve($manager, 1, $decision);
    check($db->table('transaction_sell_lines')->where('id', 1)->value('quantity') === '1.0000', 'manager applies exact requested quantities');
    check($db->table('variation_location_details')->value('qty_available') ===
        ($disposition === 'waste' ? $stock : bcadd($stock, '5', 4)), 'manager disposition controls stock return '.$disposition);
    check($db->table('restaurant_approvals')->value('status') === 'applied', 'approval retains manager and reason audit');
    if ($disposition === 'waste') {
        check($db->table('purchase_lines')->where('id', 2)->value('quantity_adjusted') === '5.0000' &&
            $db->table('restaurant_waste')->count() === 2, 'waste remains consumed in purchase ledger with explicit loss records');
    }
    $after = snapshot($db);
    $service->approve($manager, 1, $decision);
    check(snapshot($db) === $after, 'manager approval retry never repeats stock return or waste');
}
[$db, $service, $waiter] = restaurantFixture();
$db->table('restaurant_bills')->delete();
$db->table('saved_sale_split_operations')->delete();
$db->table('transactions')->where('id', 1)->update(['invoice_no' => 'OLD', 'res_table_id' => null]);
$create = ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'location_id' => 2, 'table_id' => 1,
    'contact_id' => 3, 'items' => [['variation_id' => 5, 'quantity' => '1.2500']]];
$result = $service->create($waiter, $create);
check($result['state'] === 'open' && $result['payment_status'] === 'due' && $result['total'] === '12.5000', 'waiter creates server-priced unpaid table bill');
$after = snapshot($db);
check($service->create($waiter, $create) === $result && snapshot($db) === $after, 'new table bill creation is idempotent');
$create['operation_id'] = (string) Illuminate\Support\Str::uuid();
rejects(fn () => $service->create($waiter, $create), 'restaurant_workflow');
check(snapshot($db) === $after, 'occupied table rejection never creates another bill or consumes stock');
rejects(fn () => (new App\Services\Sales\SavedSaleSplitService($db))->createRestaurantBill($waiter, 2, 3, 1, [5 => '1']), 'restaurant_requires_transaction');

[$db, $service, $waiter] = restaurantFixture();
$split = new App\Services\Sales\SavedSaleSplitService($db);
$preview = $split->preview(1, $waiter);
check(!$preview['eligible'] && $preview['reason']['code'] === 'restaurant_workflow_required', 'restaurant split preview explicitly fences ticket-unaware partitions');
rejects(fn () => $split->split(1, $waiter, ['operation_id' => (string) Illuminate\Support\Str::uuid(),
    'expected_version' => $preview['expected_version'], 'lines' => [['id' => 1, 'quantity' => '1']]]), 'restaurant_workflow_required');
$preview = $split->additionsPreview(1, $waiter);
check($preview['restaurant_managed'] === true, 'Orders editor detects managed restaurant bills from backend');
rejects(fn () => $split->editItems(1, $waiter, ['operation_id' => (string) Illuminate\Support\Str::uuid(),
    'expected_version' => $preview['expected_version'], 'items' => [['variation_id' => 5, 'quantity' => '1']]]), 'restaurant_workflow_required');
foreach (['splitPayment', 'splitPaymentDeletion'] as $method) {
    rejects(function () use ($db, $method) {
        $db->transaction(function () use ($db, $method) {
            if ($method === 'splitPayment') {
                App\Services\Sales\SavedSaleWriteGuard::splitPayment($db, 1, 1, 7, (string) Illuminate\Support\Str::uuid(),
                    str_repeat('a', 64), fn () => throw new RuntimeException('Unexpected legacy payment callback'));
            } else {
                App\Services\Sales\SavedSaleWriteGuard::splitPaymentDeletion($db, 1, 1, 7, 1,
                    fn () => throw new RuntimeException('Unexpected legacy deletion callback'));
            }
        });
    }, 'restaurant_write_fenced');
}
rejects(fn () => $service->transition($waiter, 1, restaurantInput($service, $waiter, ['action' => 'ready'])), 'restaurant_workflow');
$dispatch = restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]]);
$service->dispatch($waiter, 1, $dispatch);
$after = snapshot($db);
rejects(fn () => $service->dispatch($waiter, 1, array_replace($dispatch, ['operation_id' => (string) Illuminate\Support\Str::uuid()])), 'restaurant_workflow');
check(snapshot($db) === $after, 'stale dispatch changes neither rounds nor quantities');
$revoked = clone $waiter; $revoked->permissions = ['sell.update'];
rejects(fn () => $service->dispatch($revoked, 1, $dispatch), 'restaurant_workflow');
$other = clone $waiter; $other->business_id = 2;
rejects(fn () => $service->dispatch($other, 1, $dispatch), 'restaurant_workflow');
$db->table('restaurant_bills')->where('transaction_id', 1)->update(['waiter_id' => 8]);
rejects(fn () => $service->dispatch($waiter, 1, $dispatch), 'restaurant_workflow');
check($db->table('restaurant_tickets')->count() === 1, 'replays revalidate current tenant/location/ownership and cannot send another round');

foreach (['return_stock', 'waste'] as $disposition) {
    [$db, $service, $waiter] = restaurantFixture();
    $service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]]));
    $preview = $service->preview($waiter, 1);
    $service->edit($waiter, 1, ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'expected_version' => $preview['expected_version'],
        'restaurant_version' => $preview['restaurant_version'], 'items' => [['variation_id' => 5, 'quantity' => '8']]]);
    $preview = $service->preview($waiter, 1);
    $input = ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'expected_version' => $preview['expected_version'],
        'restaurant_version' => $preview['restaurant_version'], 'items' => [['variation_id' => 5, 'quantity' => '5']],
        'reason' => 'Cancel one sent and two unsent units', 'disposition' => $disposition];
    $stock = $db->table('variation_location_details')->value('qty_available');
    $approval = $service->edit($waiter, 1, $input);
    rejects(fn () => $service->transition($waiter, 1, restaurantInput($service, $waiter, ['action' => 'ready'])), 'restaurant_workflow');
    $manager = clone $waiter; $manager->id = 8; $manager->permissions = ['sell.update', 'location.2', 'restaurant.approve_void'];
    $service->approve($manager, 1, ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'approval_id' => $approval['approval_id'], 'decision' => 'approve']);
    check($db->table('restaurant_tickets')->where('id', 1)->value('quantity') === '5.0000' &&
        $db->table('restaurant_tickets')->where('id', 1)->value('original_quantity') === '6.0000',
        'approval cancels only dispatched excess and retains original ticket audit');
    check($db->table('variation_location_details')->value('qty_available') === bcadd($stock, $disposition === 'waste' ? '2' : '3', 4),
        'undispatched reductions return stock even when the dispatched cancellation is wasted');
    if ($disposition === 'waste') {
        $loss = '0';
        foreach ($db->table('restaurant_waste')->get() as $row) { $loss = bcadd($loss, $row->quantity, 4); }
        check($loss === '1.0000', 'waste consumption exactly matches the single cancelled prepared unit');
    }
}

[$db, $service, $waiter] = restaurantFixture();
$invalids = [null, 'bad', [], [['variation_id' => 5, 'quantity' => 1.5]], [['variation_id' => 5, 'quantity' => '1.00001']],
    [['variation_id' => 5, 'quantity' => '1'], ['variation_id' => 5, 'quantity' => '2']]];
foreach ($invalids as $items) {
    $before = snapshot($db);
    try {
        $service->create($waiter, ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'location_id' => 2, 'table_id' => 1, 'contact_id' => 3, 'items' => $items]);
        throw new RuntimeException('Invalid quantities accepted');
    } catch (App\Services\Sales\SavedSplitException $e) { check($e->httpStatus === 422, 'malformed quantity input produces an explicit client error'); }
    check(snapshot($db) === $before, 'malformed quantities never mutate inventory or workflow');
}

[$db, $service, $waiter] = restaurantFixture();
$waiter->permissions[] = 'sell.payments';
$db->table('cash_registers')->insert(['user_id' => 7, 'business_id' => 1, 'location_id' => 2, 'status' => 'open']);
$service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'kitchen', 'variations' => [5]]));
$service->transition($waiter, 1, restaurantInput($service, $waiter, ['action' => 'ready']));
$pay = restaurantInput($service, $waiter, ['payments' => [['method' => 'cash', 'amount' => '12.3456']]]);
$service->pay($waiter, 1, $pay);
check($db->table('cash_register_transactions')->value('amount') === '12.3456' &&
    $db->table('cash_register_transactions')->value('cash_register_id') === '1', 'cashier open register receives an exact decimal credit once');
$service->pay($waiter, 1, $pay);
check($db->table('cash_register_transactions')->count() === 1, 'cashier payment replay never duplicates register posting');
$invalidPay = restaurantInput($service, $waiter, ['payments' => [['method' => 'advance', 'amount' => '1']]]);
$before = snapshot($db);
rejects(fn () => $service->pay($waiter, 1, $invalidPay), 'restaurant_workflow');
check(snapshot($db) === $before, 'unsupported credit-allocation tender is rejected without touching real payments');

[$db, $service, $waiter] = restaurantFixture();
$waiter->permissions[] = 'sell.payments';
$service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]]));
$service->transition($waiter, 1, restaurantInput($service, $waiter, ['action' => 'ready']));
$db->getSchemaBuilder()->create('accounts', function (Blueprint $t) {
    $t->increments('id'); $t->integer('business_id'); $t->integer('is_closed'); $t->text('deleted_at')->nullable();
});
$db->table('accounts')->insert(['business_id' => 1, 'is_closed' => 0]);
$db->getSchemaBuilder()->table('account_transactions', function (Blueprint $t) {
    foreach (['amount', 'account_id', 'type', 'sub_type', 'operation_date', 'created_by', 'transaction_payment_id', 'note'] as $c) { $t->text($c)->nullable(); }
});
$db->table('business_locations')->update(['default_payment_accounts' => '{"cash":{"is_enabled":1,"account":1}}']);
$accountPayment = restaurantInput($service, $waiter, ['payments' => [['method' => 'cash', 'amount' => '12.3456']]]);
$before = snapshot($db);
rejects(fn () => $service->pay($waiter, 1, $accountPayment), 'restaurant_workflow');
check(snapshot($db) === $before, 'configured payment account with a disabled account module fails explicitly');
$db->table('business')->update(['enabled_modules' => '["tables","account"]']);
$before = snapshot($db);
rejects(fn () => $service->pay($waiter, 1, $accountPayment), 'restaurant_workflow');
check(snapshot($db) === $before, 'missing account posting listener rolls back payment rather than silently losing accounting');
$listener = new App\Listeners\AddAccountTransaction(new App\Utils\TransactionUtil(), new App\Utils\ModuleUtil());
$db->getEventDispatcher()->listen(App\Events\TransactionPaymentAdded::class, [$listener, 'handle']);
$service->pay($waiter, 1, $accountPayment);
check($db->table('account_transactions')->value('amount') === '12.3456' &&
    $db->table('account_transactions')->value('type') === 'credit', 'real backend event posts exact configured-account amount');
$service->pay($waiter, 1, $accountPayment);
check($db->table('account_transactions')->count() === 1, 'idempotent retries never duplicate account credits');

[$db, $service, $waiter] = restaurantFixture();
$waiter->permissions[] = 'sell.payments';
$service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]]));
$service->transition($waiter, 1, restaurantInput($service, $waiter, ['action' => 'ready']));
$db->statement("CREATE TRIGGER reject_restaurant_audit BEFORE INSERT ON restaurant_operations BEGIN SELECT RAISE(ABORT, 'Synthetic final audit failure'); END");
$before = snapshot($db);
$payment = restaurantInput($service, $waiter, ['payments' => [['method' => 'cash', 'amount' => (string) $db->table('transactions')->where('id', 1)->value('final_total')]]]);
try {
    $service->pay($waiter, 1, $payment);
    throw new RuntimeException('Expected failed durable audit');
} catch (Illuminate\Database\QueryException $e) {
    check(str_contains($e->getMessage(), 'Synthetic final audit failure'), 'test injects failure after final payment and fiscal scheduling');
}
check(snapshot($db) === $before && $service->scheduled === 0, 'final audit failure rolls back payments, header, stock and after-commit fiscal dispatch');

[$db, $service, $waiter] = restaurantFixture();
$superadmin = clone $waiter; $superadmin->id = 8; $superadmin->permissions = ['superadmin'];
$preview = $service->preview($superadmin, 1);
check($preview['eligible'] === true, 'tenant-scoped superadmin restaurant edit matches backend capability and ownership rules');
$db->table('restaurant_bills')->delete();
rejects(fn () => (new App\Services\Sales\SavedSaleSplitService($db))->additionsPreview(1, $waiter), 'unsupported_restaurant_history');
check($db->table('transaction_sell_lines')->count() === 2, 'allowing managed table fields does not enable the saved retail editor for legacy kitchen bills');

[$db, $service, $waiter] = restaurantFixture();
$db->table('restaurant_bills')->delete();
$db->table('saved_sale_split_operations')->delete();
$db->table('transactions')->where('id', 1)->update(['res_table_id' => null, 'res_waiter_id' => null]);
$db->table('variations')->where('id', 5)->update(['default_sell_price' => '0']);
$before = snapshot($db);
rejects(fn () => $service->create($waiter, ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'location_id' => 2,
    'table_id' => 1, 'contact_id' => 3, 'items' => [['variation_id' => 5, 'quantity' => '1']]]), 'restaurant_positive_total_required');
check(snapshot($db) === $before, 'zero-total bill rejection leaves no permanently occupied/unpayable table or invoice allocation');

[$db, $service, $waiter] = restaurantFixture();
$waiter->permissions[] = 'restaurant.bar';
$service->dispatch($waiter, 1, restaurantInput($service, $waiter, ['station' => 'bar', 'variations' => [5]]));
$preview = $service->preview($waiter, 1);
$approval = $service->edit($waiter, 1, ['operation_id' => (string) Illuminate\Support\Str::uuid(),
    'expected_version' => $preview['expected_version'], 'restaurant_version' => $preview['restaurant_version'],
    'items' => [['variation_id' => 5, 'quantity' => '1']], 'reason' => 'Stale test cancellation', 'disposition' => 'return_stock']);
$service->ticket($waiter, 1, ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'station' => 'bar', 'ticket_id' => 1, 'status' => 'cooked']);
$manager = clone $waiter; $manager->id = 8; $manager->permissions[] = 'restaurant.approve_void';
check($service->approvals($manager, 2)[0]['is_stale'], 'preparation after an approval request marks the review stale');
$decision = ['operation_id' => (string) Illuminate\Support\Str::uuid(), 'approval_id' => $approval['approval_id'], 'decision' => 'approve'];
$before = snapshot($db);
rejects(fn () => $service->approve($manager, 1, $decision), 'restaurant_workflow');
check(snapshot($db) === $before, 'stale approval cannot change stock, tickets or bill quantities');
$decision['operation_id'] = (string) Illuminate\Support\Str::uuid(); $decision['decision'] = 'reject';
$service->approve($manager, 1, $decision);
check($db->table('restaurant_approvals')->value('status') === 'rejected' && $db->table('transaction_sell_lines')->count() === 2,
    'manager can reject a stale request without applying its changes');

echo "PASS: $checks total isolated restaurant/saved-sale checks. No live writes or fiscal requests.\n";
