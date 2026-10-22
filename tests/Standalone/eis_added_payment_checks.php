<?php

// Real payment controller/utilities, SQLite memory, local event observer; no kernel or EIS calls.
require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Transaction;
use App\TransactionPayment;
use App\Http\Controllers\TransactionPaymentController;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Modules\EIS\Events\SaleCompleted;

class Module
{
    public static function toCollection() { return collect([]); }
}
$container = new Container();
$capsule = new Capsule($container);
$capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
$events = new Illuminate\Events\Dispatcher($container);
$capsule->setEventDispatcher($events);
$capsule->setAsGlobal(); $capsule->bootEloquent();
Container::setInstance($container);
$container->instance('db', $capsule->getDatabaseManager());
$container->instance('events', $events);
$container->instance('translator', new Illuminate\Translation\Translator(new Illuminate\Translation\ArrayLoader(), 'en'));
$container->instance('log', new Psr\Log\NullLogger());
$config = new Illuminate\Config\Repository([
    'database' => ['default' => 'default', 'connections' => ['default' => [
        'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
    ]]],
    'auth' => ['defaults' => ['guard' => 'fixture'], 'guards' => ['fixture' => ['driver' => 'fixture']]],
    'activitylog' => ['enabled' => true, 'default_log_name' => 'default', 'activity_model' => \Spatie\Activitylog\Models\Activity::class, 'table_name' => 'activity_log'],
]);
$container->instance('config', $config);
$container->instance(Illuminate\Contracts\Config\Repository::class, $config);
Facade::setFacadeApplication($container); Facade::clearResolvedInstances();
if (! class_exists('DB')) { class_alias(DB::class, 'DB'); }
if (! class_exists('Log')) { class_alias(Illuminate\Support\Facades\Log::class, 'Log'); }
if (! class_exists('Carbon')) { class_alias(Carbon\Carbon::class, 'Carbon'); }
$db = $capsule->getConnection();
$db->setTransactionManager(new Illuminate\Database\DatabaseTransactionsManager());
$db->getPdo()->sqliteCreateFunction('IF', fn ($condition, $yes, $no) => $condition ? $yes : $no, 3);
$schema = $db->getSchemaBuilder();
$schema->create('transactions', function (Blueprint $t) {
    $t->increments('id'); $t->integer('business_id'); $t->integer('contact_id'); $t->integer('location_id')->nullable();
    $t->string('type'); $t->string('status'); $t->string('payment_status');
    $t->decimal('final_total', 22, 4); $t->string('transaction_date'); $t->timestamps();
});
$schema->create('contacts', function (Blueprint $t) {
    $t->increments('id'); $t->integer('business_id'); $t->decimal('balance', 22, 4)->default(0); $t->softDeletes();
});
$schema->create('transaction_payments', function (Blueprint $t) {
    $t->increments('id');
    foreach (['transaction_id', 'business_id', 'payment_for', 'created_by', 'parent_id'] as $column) { $t->integer($column)->nullable(); }
    foreach (['method', 'note', 'card_number', 'card_holder_name', 'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
        'cheque_number', 'bank_account_number', 'paid_on', 'transaction_no', 'payment_ref_no', 'document'] as $column) { $t->string($column)->nullable(); }
    $t->decimal('amount', 22, 4); $t->boolean('is_return')->default(false); $t->timestamps();
});
$schema->create('saved_sale_split_operations', function (Blueprint $t) {
    $t->increments('id'); $t->integer('business_id'); $t->integer('user_id');
    $t->string('operation_id')->unique(); $t->integer('source_id'); $t->integer('result_id')->nullable();
    $t->string('payload_hash'); $t->string('expected_version'); $t->text('allocation_json');
    $t->text('response_json')->nullable(); $t->timestamps();
});
$schema->create('business', function (Blueprint $t) {
    $t->increments('id'); $t->text('custom_labels')->nullable(); $t->string('time_zone')->default('UTC');
});
$schema->create('business_locations', function (Blueprint $t) {
    $t->increments('id'); $t->integer('business_id'); $t->string('name');
    $t->text('default_payment_accounts')->nullable();
});
$schema->create('activity_log', function (Blueprint $t) {
    $t->increments('id'); $t->string('log_name')->nullable(); $t->text('description');
    $t->string('subject_type')->nullable(); $t->integer('subject_id')->nullable();
    $t->string('causer_type')->nullable(); $t->integer('causer_id')->nullable();
    $t->string('batch_uuid')->nullable(); $t->text('properties')->nullable();
    $t->integer('business_id')->nullable(); $t->timestamps();
});
DB::table('contacts')->insert(['id' => 1, 'business_id' => 7]);
DB::table('business')->insert(['id' => 7, 'time_zone' => 'UTC']);
DB::table('business_locations')->insert([
    'id' => 2, 'business_id' => 7, 'name' => 'Fixture Location',
    'default_payment_accounts' => json_encode([
        'cash' => ['is_enabled' => 1], 'card' => ['is_enabled' => 1],
        'bank_transfer' => ['is_enabled' => 1],
    ]),
]);
$session = new Illuminate\Session\Store('fixture', new Illuminate\Session\ArraySessionHandler(120));
$session->put('user.business_id', 7); $session->put('business.id', 7);
$request = Request::create('https://fixture.invalid/payments'); $request->setLaravelSession($session);
$container->instance('request', $request); $container->instance('session', $session);
$redirector = new Illuminate\Routing\Redirector(new Illuminate\Routing\UrlGenerator(new Illuminate\Routing\RouteCollection(), $request));
$redirector->setSession($session); $container->instance('redirect', $redirector);
$user = Mockery::mock(App\User::class)->makePartial();
$user->id = 1; $user->business_id = 7; $user->shouldReceive('can')->andReturn(true);
$authGuard = Mockery::mock();
$authGuard->shouldReceive('user')->andReturn($user);
$auth = Mockery::mock(Illuminate\Auth\AuthManager::class);
$auth->shouldReceive('guard')->andReturn($authGuard);
$auth->shouldReceive('user')->andReturn($user);
$container->instance(Illuminate\Auth\AuthManager::class, $auth);
$container->instance(Illuminate\Contracts\Auth\Factory::class, $auth);
$container->instance('auth', $auth);
/** @var TransactionUtil&\Mockery\MockInterface $util */
$util = Mockery::mock(TransactionUtil::class)->makePartial();
$util->shouldReceive('num_uf')->andReturnUsing(fn ($amount) => (float) $amount);
$util->shouldReceive('uf_date')->andReturn('2026-10-14 10:00:00');
$util->shouldReceive('setAndGetReferenceCount')->andReturn(1);
$util->shouldReceive('generateReferenceNumber')->andReturn('FIXTURE');
$util->shouldReceive('uploadFile')->andReturn(null);
$util->shouldReceive('activityLog')->andReturn(null);
$controller = new TransactionPaymentController($util, new ModuleUtil());
$checks = 0; $submitted = [];
function paymentCheck($condition, string $message): void {
    global $checks;
    if (! $condition) { throw new RuntimeException($message); } ++$checks;
}
$events->listen(SaleCompleted::class, function (SaleCompleted $event) use (&$submitted) {
    paymentCheck(DB::transactionLevel() === 0, 'Fiscal event only after outer commit');
    $sale = $event->transaction->fresh();
    paymentCheck($sale->payment_status === 'paid' && $sale->type === 'sell' && $sale->status === 'final', 'Committed final paid sale');
    paymentCheck((new TransactionUtil())->getTotalPaid($sale->id) >= $sale->final_total, 'Payments exist when event fires');
    $submitted[] = $sale->id;
});
function saleFixture(string $type = 'sell', string $status = 'final'): Transaction {
    return Transaction::create(['business_id' => 7, 'contact_id' => 1, 'type' => $type, 'status' => $status,
        'location_id' => 2, 'payment_status' => 'due', 'final_total' => 100, 'transaction_date' => '2026-10-14 10:00:00']);
}
function postPayment(Transaction $sale, $amount, string $method = 'cash', ?string $operation = null): array {
    global $controller, $session, $container;
    $data = ['transaction_id' => $sale->id, 'amount' => $amount, 'method' => $method, 'paid_on' => '2026-10-14'];
    if ($operation !== null) { $data['saved_split_operation_id'] = $operation; }
    $request = Request::create('/payments', 'POST', $data);
    $request->setLaravelSession($session); $container->instance('request', $request);
    $controller->store($request);
    return $session->get('status');
}
function addPayment(Transaction $sale, $amount, string $method = 'cash', ?string $operation = null): void {
    paymentCheck(postPayment($sale, $amount, $method, $operation)['success'] === true, 'Payment saved successfully');
}
function deletePayment(TransactionPayment $payment): array {
    global $controller, $session, $container;
    $request = Request::create('/payments/'.$payment->id, 'DELETE', [], [], [], [
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
    ]);
    $request->setLaravelSession($session); $container->instance('request', $request);
    return $controller->destroy($payment->id);
}
function trackSplit(Transaction $sale): void {
    DB::table('saved_sale_split_operations')->insert([
        'business_id' => $sale->business_id, 'user_id' => 1,
        'operation_id' => (string) Illuminate\Support\Str::uuid(),
        'source_id' => $sale->id, 'result_id' => $sale->id,
        'payload_hash' => str_repeat('a', 64), 'expected_version' => str_repeat('a', 64),
        'allocation_json' => json_encode(['kind' => 'split']), 'response_json' => '{}',
    ]);
}
foreach (['cash', 'bank_transfer', 'card'] as $method) {
    $sale = saleFixture(); $before = count($submitted);
    addPayment($sale, 60, $method);
    paymentCheck($sale->fresh()->payment_status === 'partial' && count($submitted) === $before, 'Partial payment does not initiate EIS');
    addPayment($sale, 40, $method);
    paymentCheck($sale->fresh()->payment_status === 'paid' && array_slice($submitted, $before) === [$sale->id], 'Zero balance initiates EIS once for '.$method);
    $paymentCount = TransactionPayment::where('transaction_id', $sale->id)->count();
    addPayment($sale, 40, $method);
    paymentCheck(count($submitted) === $before + 1 && TransactionPayment::where('transaction_id', $sale->id)->count() === $paymentCount, 'Already paid retry adds no payment or submission');
}
foreach ([['purchase', 'final'], ['sell', 'draft'], ['sell_return', 'final']] as [$type, $status]) {
    $sale = saleFixture($type, $status); $before = count($submitted);
    addPayment($sale, 100);
    paymentCheck($sale->fresh()->payment_status === 'paid' && count($submitted) === $before, 'No EIS for '.$type.'/'.$status);
}
$sale = saleFixture(); $before = count($submitted);
DB::beginTransaction(); addPayment($sale, 100);
paymentCheck(count($submitted) === $before, 'Nested payment commit defers submission');
DB::rollBack();
paymentCheck(count($submitted) === $before && $sale->fresh()->payment_status === 'due'
    && TransactionPayment::where('transaction_id', $sale->id)->count() === 0, 'Rollback discards payment and fiscal callback');

foreach (['cash', 'card', 'bank_transfer'] as $method) {
    $sale = saleFixture(); trackSplit($sale); $before = count($submitted);
    $firstOperation = (string) Illuminate\Support\Str::uuid();
    addPayment($sale, 60, $method, $firstOperation);
    paymentCheck($sale->fresh()->payment_status === 'partial', 'Split sale accepts partial '.$method.' payment');

    $paymentCount = TransactionPayment::where('transaction_id', $sale->id)->count();
    addPayment($sale, 60, $method, $firstOperation);
    paymentCheck(TransactionPayment::where('transaction_id', $sale->id)->count() === $paymentCount,
        'Split payment retry with the same operation does not duplicate payment');

    addPayment($sale, 40, $method, (string) Illuminate\Support\Str::uuid());
    paymentCheck($sale->fresh()->payment_status === 'paid' && array_slice($submitted, $before) === [$sale->id],
        'Split sale full settlement submits once for '.$method);
    paymentCheck(\App\Services\Sales\SavedSaleWriteGuard::fiscal(DB::connection(), $sale->id)->payment_status === 'paid',
        'Paid split sale passes the guarded fiscal check');
}

$sale = saleFixture(); trackSplit($sale);
$operation = (string) Illuminate\Support\Str::uuid();
addPayment($sale, 25, 'cash', $operation);
$paymentCount = TransactionPayment::where('transaction_id', $sale->id)->count();
$collision = postPayment($sale, 26, 'cash', $operation);
paymentCheck($collision['success'] === false && $sale->fresh()->payment_status === 'partial'
    && TransactionPayment::where('transaction_id', $sale->id)->count() === $paymentCount,
    'Split payment operation cannot be replayed with a changed payload');

$sale = saleFixture(); trackSplit($sale);
$missingOperation = postPayment($sale, 25);
paymentCheck($missingOperation['success'] === false && $sale->fresh()->payment_status === 'due'
    && TransactionPayment::where('transaction_id', $sale->id)->count() === 0,
    'Split payment requires the idempotency token supplied by the payment form');

$sale = saleFixture(); trackSplit($sale);
DB::beginTransaction();
try {
    \App\Services\Sales\SavedSaleWriteGuard::splitPayment(
        DB::connection(), $sale->id, $sale->business_id, 1,
        (string) Illuminate\Support\Str::uuid(), str_repeat('b', 64),
        function () use ($sale) {
            $sale->final_total = 1;
            $sale->save();
            return ['success' => true, 'payment_id' => 1];
        }
    );
    DB::commit();
    throw new RuntimeException('Expected split header mutation to be rejected');
} catch (\App\Services\Sales\SavedSplitException $e) {
    DB::rollBack();
    paymentCheck($e->errorCode === 'split_write_fenced', 'Split payment scope rejects sale repricing');
}
paymentCheck((float) $sale->fresh()->final_total === 100.0
    && TransactionPayment::where('transaction_id', $sale->id)->count() === 0,
    'Rejected split header mutation rolls back completely');

$sale = saleFixture(); trackSplit($sale);
addPayment($sale, 60, 'cash', (string) Illuminate\Support\Str::uuid());
$splitPayment = TransactionPayment::where('transaction_id', $sale->id)->firstOrFail();
DB::beginTransaction();
try {
    $splitPayment->delete();
    DB::commit();
    throw new RuntimeException('Expected direct split payment deletion to be rejected');
} catch (\App\Services\Sales\SavedSplitException $e) {
    DB::rollBack();
    paymentCheck($e->errorCode === 'split_write_fenced', 'Direct split payment deletion remains fenced');
}
paymentCheck(TransactionPayment::whereKey($splitPayment->id)->exists(), 'Rejected direct deletion preserves the split payment');
$deleteResponse = deletePayment($splitPayment);
paymentCheck($deleteResponse['success'] === true
    && !TransactionPayment::whereKey($splitPayment->id)->exists()
    && $sale->fresh()->payment_status === 'due',
    'Split payment deletion uses the scoped guard and recalculates status');
$deleteOperation = DB::table('saved_sale_split_operations')->where('source_id', $sale->id)
    ->where('result_id', $sale->id)->orderByDesc('id')->first();
paymentCheck(json_decode($deleteOperation->allocation_json, true)['kind'] === 'payment_delete'
    && !empty($deleteOperation->response_json), 'Split payment deletion is recorded in the durable ledger');

$ordinarySale = saleFixture();
addPayment($ordinarySale, 50);
$ordinaryPayment = TransactionPayment::where('transaction_id', $ordinarySale->id)->firstOrFail();
paymentCheck(deletePayment($ordinaryPayment)['success'] === true
    && !TransactionPayment::whereKey($ordinaryPayment->id)->exists()
    && $ordinarySale->fresh()->payment_status === 'due',
    'Ordinary sale payment deletion remains unchanged');

// Edited partial payment becoming paid.
$sale = saleFixture(); addPayment($sale, 50); $before = count($submitted);
$payment = TransactionPayment::where('transaction_id', $sale->id)->firstOrFail();
$request = Request::create('/payments/'.$payment->id, 'POST', ['amount' => 100, 'method' => 'cash', 'paid_on' => '2026-10-14']);
$request->setLaravelSession($session); $container->instance('request', $request);
$controller->update($request, $payment->id);
paymentCheck($session->get('status')['success'] === true && array_slice($submitted, $before) === [$sale->id], 'Edited payment clearing balance initiates EIS');
$controller->update($request, $payment->id);
paymentCheck(count($submitted) === $before + 1, 'Editing already paid sale does not repeat submission');

// Contact-due distribution pays two invoices and only partly pays the third.
DB::table('transactions')->update(['contact_id' => 99]);
$first = saleFixture(); $second = saleFixture(); $third = saleFixture();
$parent = TransactionPayment::create(['business_id' => 7, 'payment_for' => 1, 'amount' => 250, 'method' => 'cash', 'paid_on' => '2026-10-14']);
$before = count($submitted); DB::beginTransaction();
$remaining = $util->payAtOnce($parent, 'sell');
paymentCheck(count($submitted) === $before, 'Contact due submissions deferred until allocations commit');
DB::commit();
paymentCheck($remaining == 0 && array_slice($submitted, $before) === [$first->id, $second->id], 'Each fully settled invoice initiates EIS');
paymentCheck($third->fresh()->payment_status === 'partial', 'Partial allocated invoice not submitted');

// Failed child-payment insert rolls back statuses, with no fiscal event.
DB::table('transactions')->update(['contact_id' => 99]); $sale = saleFixture();
$parent = TransactionPayment::create(['business_id' => 7, 'payment_for' => 1, 'amount' => 100, 'method' => 'cash']);
$before = count($submitted);
DB::statement("CREATE TRIGGER reject_allocation BEFORE INSERT ON transaction_payments BEGIN SELECT RAISE(ABORT, 'fixture failure'); END");
DB::beginTransaction();
try { $util->payAtOnce($parent, 'sell'); throw new RuntimeException('Expected allocation failure'); }
catch (Illuminate\Database\QueryException $e) { DB::rollBack(); }
paymentCheck(count($submitted) === $before && $sale->fresh()->payment_status === 'due', 'Failed allocation cannot dispatch fiscal event');
Mockery::close();
echo "PASS: {$checks} EIS added-payment checks (SQLite memory and local events only).\n";