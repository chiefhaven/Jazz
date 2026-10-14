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
Facade::setFacadeApplication($container); Facade::clearResolvedInstances();
if (! class_exists('DB')) { class_alias(DB::class, 'DB'); }
if (! class_exists('Log')) { class_alias(Illuminate\Support\Facades\Log::class, 'Log'); }
if (! class_exists('Carbon')) { class_alias(Carbon\Carbon::class, 'Carbon'); }
$db = $capsule->getConnection();
$db->setTransactionManager(new Illuminate\Database\DatabaseTransactionsManager());
$db->getPdo()->sqliteCreateFunction('IF', fn ($condition, $yes, $no) => $condition ? $yes : $no, 3);
$schema = $db->getSchemaBuilder();
$schema->create('transactions', function (Blueprint $t) {
    $t->increments('id'); $t->integer('business_id'); $t->integer('contact_id');
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
DB::table('contacts')->insert(['id' => 1, 'business_id' => 7]);
$session = new Illuminate\Session\Store('fixture', new Illuminate\Session\ArraySessionHandler(120));
$session->put('user.business_id', 7); $session->put('business.id', 7);
$request = Request::create('https://fixture.invalid/payments'); $request->setLaravelSession($session);
$container->instance('request', $request); $container->instance('session', $session);
$redirector = new Illuminate\Routing\Redirector(new Illuminate\Routing\UrlGenerator(new Illuminate\Routing\RouteCollection(), $request));
$redirector->setSession($session); $container->instance('redirect', $redirector);
$user = Mockery::mock(App\User::class)->makePartial();
$user->id = 1; $user->business_id = 7; $user->shouldReceive('can')->andReturn(true);
$auth = Mockery::mock(); $auth->shouldReceive('user')->andReturn($user);
$container->instance(Illuminate\Contracts\Auth\Factory::class, $auth);
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
        'payment_status' => 'due', 'final_total' => 100, 'transaction_date' => '2026-10-14 10:00:00']);
}
function addPayment(Transaction $sale, $amount, string $method = 'cash'): void {
    global $controller, $session, $container;
    $request = Request::create('/payments', 'POST', ['transaction_id' => $sale->id, 'amount' => $amount, 'method' => $method, 'paid_on' => '2026-10-14']);
    $request->setLaravelSession($session); $container->instance('request', $request);
    $controller->store($request);
    paymentCheck($session->get('status')['success'] === true, 'Payment saved successfully');
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