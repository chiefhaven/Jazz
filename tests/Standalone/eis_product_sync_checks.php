<?php

// Bare SQLite memory fixture; no kernel, persistent database, or network.
require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Product;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Facade;
use Modules\EIS\Models\EisProductMap;
use Modules\EIS\Services\Products\EisProductClient;
use Modules\EIS\Services\Products\ProductTransformer;
use Modules\EIS\Services\Products\ProductUpsertService;
use Modules\EIS\Services\Sync\ProductSyncService;

class CatalogFixtureClient extends EisProductClient
{
    public array $pages = [];
    public array $requests = [];
    public function fetch($settings, int $page = 1)
    {
        $this->requests[] = $page;
        $response = $this->pages[$page - 1] ?? null;
        if ($response instanceof Throwable) {
            throw $response;
        }
        return $response;
    }
}

$checks = 0;
function catalogCheck(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    ++$checks;
}
function catalogFixture(): array
{
    $app = new Container();
    Container::setInstance($app);
    $capsule = new Capsule($app);
    $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
    $capsule->setEventDispatcher(new Illuminate\Events\Dispatcher($app));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    Illuminate\Database\Eloquent\Model::clearBootedModels();
    $app->instance('db', $capsule->getDatabaseManager());
    $app->instance('log', new Psr\Log\NullLogger());
    $app['config']['eis.system_user_id'] = 1;
    $app['config']['eis.base_url'] = 'https://fixture.invalid';
    Facade::setFacadeApplication($app);
    Facade::clearResolvedInstances();
    $db = $capsule->getConnection();
    foreach ([
        'business' => [],
        'products' => ['business_id', 'type', 'created_by', 'enable_stock', 'name', 'product_description', 'sku', 'tax',
            'eis_product_id', 'eis_last_synced_at', 'unit_id', 'expiry_period', 'deleted_at'],
        'product_variations' => ['product_id', 'variation_template_id', 'name', 'is_dummy'],
        'variations' => ['product_id', 'product_variation_id', 'name', 'default_sell_price', 'default_purchase_price',
            'dpp_inc_tax', 'sell_price_inc_tax', 'sub_sku', 'profit_percent', 'deleted_at'],
        'business_locations' => ['business_id', 'eis_site_id', 'name'],
        'product_locations' => ['product_id', 'location_id'],
        'units' => ['business_id', 'short_name', 'actual_name'],
        'tax_rates' => ['business_id', 'tax_rate_id', 'name', 'is_activated', 'rate', 'deleted_at'],
        'eis_product_maps' => ['business_id', 'product_id', 'eis_product_id', 'sku', 'last_synced_at', 'meta', 'deleted_at'],
        'variation_location_details' => ['product_id', 'product_variation_id', 'variation_id', 'location_id', 'qty_available'],
    ] as $name => $columns) {
        $db->getSchemaBuilder()->create($name, function (Blueprint $table) use ($name, $columns) {
            $table->increments('id');
            foreach (array_merge($columns, ['created_at', 'updated_at']) as $column) {
                $table->text($column)->nullable();
            }
            if ($name === 'eis_product_maps') {
                $table->unique(['business_id', 'eis_product_id']);
                $table->unique(['business_id', 'product_id']);
            }
        });
    }
    $db->table('business')->insert([['id' => 1], ['id' => 2]]);
    $db->table('business_locations')->insert([
        ['id' => 1, 'business_id' => 1, 'eis_site_id' => 'SITE-A'],
        ['id' => 2, 'business_id' => 2, 'eis_site_id' => 'SITE-A'],
    ]);
    $db->table('units')->insert(['business_id' => 1, 'short_name' => 'pcs', 'actual_name' => 'Pieces']);
    $db->table('tax_rates')->insert(['business_id' => 1, 'tax_rate_id' => 'A', 'rate' => '0']);
    $client = new CatalogFixtureClient();
    $sync = new ProductSyncService($client, new ProductTransformer(), new ProductUpsertService());
    return [$db, $client, $sync];
}
function catalogItem(string $id, $quantity = '12.3456'): array
{
    return ['productCode' => $id, 'productName' => 'Product '.$id, 'quantity' => $quantity,
        'isProduct' => true, 'taxRateId' => 'A', 'price' => '10', 'cost' => '3'];
}
function pullCatalog($sync): void
{
    $sync->sync((object) ['business_id' => 1, 'site_id' => 'SITE-A'], 1);
}
function catalogSnapshot($db): array
{
    $result = [];
    foreach (['products', 'eis_product_maps', 'product_variations', 'variations', 'product_locations',
        'variation_location_details'] as $table) {
        $result[$table] = $db->table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all();
    }
    return $result;
}
function rejectCatalog($db, $sync, string $message): void
{
    $before = catalogSnapshot($db);
    try {
        pullCatalog($sync);
    } catch (Throwable $e) {
        catalogCheck(catalogSnapshot($db) === $before, $message.' preserves all committed data');
        return;
    }
    throw new RuntimeException($message.' should fail');
}

[$db, $client, $sync] = catalogFixture();
$db->table('products')->insert([
    ['id' => 100, 'business_id' => 1, 'name' => 'Manual', 'sku' => 'LOCAL'],
    ['id' => 101, 'business_id' => 2, 'name' => 'Other business', 'sku' => 'OTHER'],
]);
$client->pages = [['data' => [catalogItem('ONE')], 'has_more' => true],
    ['data' => [catalogItem('TWO', '0')], 'has_more' => false]];
pullCatalog($sync);
catalogCheck($client->requests === [1, 2], 'All catalog pages are fetched');
catalogCheck(Product::where('business_id', 1)->count() === 2
    && Product::withTrashed()->find(100)->trashed(), 'Unmapped manual business products are soft deleted');
catalogCheck(!Product::findOrFail(101)->trashed(), 'Other businesses remain unchanged');
$one = EisProductMap::where('eis_product_id', 'ONE')->firstOrFail();
$two = EisProductMap::where('eis_product_id', 'TWO')->firstOrFail();
catalogCheck($db->table('variation_location_details')->where('product_id', $one->product_id)->value('qty_available') === '12.3456',
    'Stock exactly matches four-decimal EIS quantity');
catalogCheck($db->table('variation_location_details')->where('product_id', $two->product_id)->value('qty_available') === '0.0000',
    'Explicit zero stock is persisted');
$db->table('business_locations')->insert(['id' => 3, 'business_id' => 1, 'eis_site_id' => 'SITE-B']);
$stockRow = (array) $db->table('variation_location_details')->where('product_id', $one->product_id)->first();
unset($stockRow['id']);
$db->table('variation_location_details')->insert(array_replace($stockRow, ['location_id' => 3, 'qty_available' => '42.0000']));
$db->table('variation_location_details')->where('product_id', $one->product_id)->where('location_id', 1)
    ->update(['qty_available' => '999.0000']);
$db->table('products')->where('id', $one->product_id)->update(['enable_stock' => 0]);
$client->pages = [['data' => [catalogItem('ONE', '1.0001')]]];
pullCatalog($sync);
catalogCheck($db->table('variation_location_details')->where('product_id', $one->product_id)->value('qty_available') === '1.0001',
    'Repull replaces local stock instead of adding or retaining the old quantity');
catalogCheck((bool) Product::findOrFail($one->product_id)->enable_stock, 'EIS stock management applies to existing products');
catalogCheck($db->table('variation_location_details')->where('location_id', 3)->value('qty_available') === '42.0000',
    'Sync overwrites only the exact EIS site location quantity');
catalogCheck(Product::withTrashed()->find($two->product_id)->trashed()
    && EisProductMap::withTrashed()->find($two->id)->trashed(), 'Absent product and its map are soft deleted together');
$client->pages = [['data' => [catalogItem('ONE', '0'), catalogItem('TWO', '999999999999999999.9999')]]];
pullCatalog($sync);
catalogCheck(Product::find($two->product_id) !== null && EisProductMap::find($two->id) !== null,
    'Returning product and map restore the same identities');
catalogCheck($db->table('variation_location_details')->where('product_id', $two->product_id)->value('qty_available')
    === '999999999999999999.9999', 'DECIMAL(22,4) precision is not lost through floats');
catalogCheck($db->table('variation_location_details')->where('product_id', $one->product_id)->value('qty_available') === '0.0000',
    'Repull can reduce existing stock to zero');
$snapshot = catalogSnapshot($db);
pullCatalog($sync);
catalogCheck(Product::where('business_id', 1)->count() === 2 && EisProductMap::where('business_id', 1)->count() === 2
    && $db->table('variation_location_details')->count() === 3, 'Repeated sync does not duplicate identities or stock rows');

foreach ([
    null, [], ['error' => 'expired token'], ['data' => null], ['data' => 'invalid'],
    ['data' => [], 'success' => false], ['data' => [], 'has_more' => 'invalid'],
    ['data' => [], 'has_more' => null], ['data' => [], 'error' => 'Unauthorized'],
    ['data' => [], 'has_more' => true], ['data' => [['productName' => 'No identifier']]],
    ['data' => [catalogItem('ONE', null)]], ['data' => [catalogItem('ONE', '-1')]],
    ['data' => [catalogItem('ONE', '1.12345')]], ['data' => [catalogItem('ONE', 'invalid')]],
    ['data' => [array_merge(catalogItem('ONE'), ['stock' => '99'])]],
    ['data' => [array_merge(catalogItem('ONE'), ['siteId' => 'ANOTHER-SITE'])]],
    ['data' => [catalogItem('ONE'), catalogItem('ONE')]],
] as $response) {
    $client->pages = [$response];
    rejectCatalog($db, $sync, 'Malformed or incomplete catalog');
}
$client->pages = [['data' => [catalogItem('ONE')], 'has_more' => true], new RuntimeException('Network failed')];
rejectCatalog($db, $sync, 'Later-page network failure');
$client->pages = [['data' => [catalogItem('ONE')], 'has_more' => true],
    ['data' => [catalogItem('ONE')], 'has_more' => true]];
rejectCatalog($db, $sync, 'Repeating page');

// A later upsert failure must roll back earlier restores, quantities, and new products.
$db->table('products')->where('id', $two->product_id)->update(['type' => 'variable', 'deleted_at' => now()]);
$db->table('eis_product_maps')->where('id', $two->id)->update(['deleted_at' => now()]);
$client->pages = [['data' => [catalogItem('NEW'), catalogItem('ONE', '77'), catalogItem('TWO')]]];
rejectCatalog($db, $sync, 'Later product import failure');
$db->table('products')->where('id', $two->product_id)->update(['type' => 'single']);
$client->pages = [['data' => []]];
pullCatalog($sync);
catalogCheck(Product::where('business_id', 1)->count() === 0 && EisProductMap::where('business_id', 1)->count() === 0,
    'A successful explicit empty catalog soft deletes all business products and maps');
catalogCheck(Product::where('business_id', 2)->count() === 1, 'Empty catalog remains tenant scoped');
$client->pages = [['data' => [catalogItem('TWO')]]];
pullCatalog($sync);
catalogCheck(Product::find($two->product_id) !== null && EisProductMap::find($two->id) !== null,
    'Restoration works after an empty catalog');
$db->table('variations')->where('product_id', $two->product_id)->update(['deleted_at' => now()]);
pullCatalog($sync);
catalogCheck($db->table('variations')->where('product_id', $two->product_id)->count() === 1
    && $db->table('variations')->where('product_id', $two->product_id)->value('deleted_at') === null,
    'Returning single variations restore instead of creating duplicate stock identities');
$productId = $db->table('products')->insertGetId([
    'business_id' => 1, 'type' => 'single', 'name' => 'Manual match', 'sku' => 'MANUAL', 'deleted_at' => now(),
]);
$client->pages = [['data' => [catalogItem('MANUAL')]]];
pullCatalog($sync);
catalogCheck(Product::find($productId) !== null
    && (int) EisProductMap::where('eis_product_id', 'MANUAL')->value('product_id') === $productId,
    'A returning catalog entry can restore a manual product by SKU and create its map');

// Reject nontransactional map storage before restoring or deleting anything.
$manager = Container::getInstance()['db'];
$engineDb = Mockery::mock(Illuminate\Database\Connection::class);
$engineDb->shouldReceive('getDriverName')->andReturn('mysql');
$engineDb->shouldReceive('getTablePrefix')->andReturn('');
$engineDb->shouldReceive('selectOne')->andReturnUsing(fn ($sql, $bindings) => (object) [
    'engine' => $bindings[0] === 'eis_product_maps' ? 'MyISAM' : 'InnoDB',
]);
$engineManager = Mockery::mock();
$engineManager->shouldReceive('connection')->andReturn($engineDb);
Illuminate\Support\Facades\DB::swap($engineManager);
try {
    rejectCatalog($db, $sync, 'Nontransactional product map storage');
} finally {
    Illuminate\Support\Facades\DB::swap($manager);
}

[$db, $client, $sync] = catalogFixture();
$db->table('business_locations')->where('business_id', 1)->update(['eis_site_id' => 'WRONG']);
$client->pages = [['data' => [catalogItem('ONE')]]];
rejectCatalog($db, $sync, 'Unknown site mapping');
$db->table('business_locations')->where('business_id', 1)->update(['eis_site_id' => 'SITE-A']);
$db->table('business_locations')->insert(['business_id' => 1, 'eis_site_id' => 'SITE-A']);
rejectCatalog($db, $sync, 'Ambiguous site mapping');
$db->table('business_locations')->where('business_id', 1)->where('id', '!=', 1)->delete();
$foreignId = $db->table('products')->insertGetId(['business_id' => 2, 'type' => 'single', 'name' => 'Foreign', 'sku' => 'ONE']);
$db->table('eis_product_maps')->insert(['business_id' => 1, 'product_id' => $foreignId, 'eis_product_id' => 'ONE']);
rejectCatalog($db, $sync, 'Cross-business map identity');

// Real client must reject HTTP failures before decoding an error response as a catalog.
$http = new Illuminate\Http\Client\Factory();
$app = Container::getInstance();
$app->instance(Illuminate\Http\Client\Factory::class, $http);
Illuminate\Support\Facades\Http::swap($http);
$http->fake(['*' => $http->response(['data' => []], 500)]);
try {
    (new EisProductClient())->fetch((object) ['jwt_token' => 'fixture', 'tpin' => 'fixture', 'site_id' => 'SITE-A']);
    throw new RuntimeException('Expected HTTP failure');
} catch (Illuminate\Http\Client\RequestException $e) {
    catalogCheck($e->response->status() === 500, 'HTTP errors cannot masquerade as empty successful catalogs');
}

Mockery::close();
echo "PASS: {$checks} isolated EIS product reconciliation checks; no live sync or network.\n";
