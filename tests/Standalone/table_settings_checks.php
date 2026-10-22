<?php

// Isolated SQLite and rendered modal checks; no live database or HTTP writes.
require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Http\Controllers\Restaurant\TableController;
use App\Restaurant\ResTable;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

$app = new Container();
Container::setInstance($app);
$capsule = new Capsule($app);
$capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$app->instance('db', $capsule->getDatabaseManager());
$app->instance('log', new Psr\Log\NullLogger());
$kernel = Mockery::mock(Illuminate\Contracts\Http\Kernel::class);
$kernel->shouldReceive('hasMiddleware')->andReturn(false);
$app->instance(Illuminate\Contracts\Http\Kernel::class, $kernel);
$app->instance('translator', new Illuminate\Translation\Translator(
    new Illuminate\Translation\FileLoader(new Illuminate\Filesystem\Filesystem(), dirname(__DIR__, 2).'\lang'), 'en'
));
Facade::setFacadeApplication($app);
Facade::clearResolvedInstances();
$app->instance('validator', new Illuminate\Validation\Factory($app['translator'], $app));
Request::macro('validate', function (array $rules) {
    return app('validator')->make($this->all(), $rules)->validate();
});
$user = Mockery::mock();
$user->shouldReceive('can')->with('access_tables')->andReturn(true);
$auth = Mockery::mock();
$auth->shouldReceive('user')->andReturn($user);
$app->instance(Illuminate\Contracts\Auth\Factory::class, $auth);
$session = new Illuminate\Session\Store('tables', new Illuminate\Session\ArraySessionHandler(120));
$session->put('user', ['id' => 1, 'business_id' => 7]);
$schema = $capsule->getConnection()->getSchemaBuilder();
$schema->create('res_tables', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('business_id');
    $table->integer('location_id');
    $table->integer('created_by');
    $table->string('name');
    $table->string('description')->nullable();
    $table->boolean('allow_multiple_orders')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
$controller = new TableController();
$checks = 0;
function tableCheck(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    ++$checks;
}
function tableRequest(array $data): Request
{
    global $app, $session;
    $request = Request::create('http://localhost/modules/tables', 'POST', $data, [], [], [
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
    ]);
    $request->setLaravelSession($session);
    $app->instance('request', $request);
    return $request;
}
$base = ['name' => 'Table 1', 'description' => 'Test table', 'location_id' => 2];
foreach ([0, 1] as $enabled) {
    $result = $controller->store(tableRequest($base + ['allow_multiple_orders' => $enabled]));
    tableCheck($result['success'] === true, 'Table creation succeeds');
    $table = $result['data']->fresh();
    tableCheck($table->allow_multiple_orders === (bool) $enabled, 'Create persists checkbox state');
    foreach ([1, 0] as $updated) {
        $result = $controller->update(tableRequest($base + ['allow_multiple_orders' => $updated]), $table->id);
        tableCheck($result['success'] === true && $table->fresh()->allow_multiple_orders === (bool) $updated,
            'Edit can enable and disable multiple orders');
    }
}
try {
    $controller->store(tableRequest($base + ['allow_multiple_orders' => 'invalid']));
    throw new RuntimeException('Expected boolean validation failure');
} catch (Illuminate\Validation\ValidationException $e) {
    tableCheck(isset($e->errors()['allow_multiple_orders']), 'Invalid checkbox values are rejected');
}

$request = $app['request'];
$routes = new Illuminate\Routing\RouteCollection();
$routes->add((new Illuminate\Routing\Route('POST', 'modules/tables', [
    'uses' => TableController::class.'@store',
    'controller' => TableController::class.'@store',
])));
$routes->add((new Illuminate\Routing\Route('PUT', 'modules/tables/{table}', [
    'uses' => TableController::class.'@update',
    'controller' => TableController::class.'@update',
])));
$url = new Illuminate\Routing\UrlGenerator($routes, $request);
$app->instance('url', $url);
$view = Mockery::mock(Illuminate\Contracts\View\Factory::class);
$html = new Collective\Html\HtmlBuilder($url, $view);
$form = new Collective\Html\FormBuilder($html, $url, $view, $session->token());
$form->setSessionStore($session);
$app->instance('form', $form);
if (!class_exists('Form')) {
    class_alias(Collective\Html\FormFacade::class, 'Form');
}
$blade = new Illuminate\View\Compilers\BladeCompiler(new Illuminate\Filesystem\Filesystem(), sys_get_temp_dir());
$business_locations = collect([2 => 'Main location']);
foreach (['create' => [false], 'edit' => [false, true]] as $modal => $states) {
    foreach ($states as $state) {
        $table = new ResTable(['name' => 'Table 1', 'description' => '', 'allow_multiple_orders' => $state]);
        $table->id = 1;
        $compiled = $blade->compileString(file_get_contents(
            dirname(__DIR__, 2).'\resources\views\restaurant\table\\'.$modal.'.blade.php'
        ));
        ob_start();
        try {
            eval('?>'.$compiled);
            $rendered = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($rendered);
        $xpath = new DOMXPath($dom);
        $checkbox = $xpath->query('//input[@type="checkbox" and @name="allow_multiple_orders"]')->item(0);
        tableCheck($checkbox !== null && $checkbox->getAttribute('class') === 'input-icheck',
            $modal.' renders the styled checkbox');
        tableCheck($checkbox->hasAttribute('checked') === $state, $modal.' renders the saved checked state');
        tableCheck($xpath->query('//input[@type="hidden" and @name="allow_multiple_orders" and @value="0"]')->length === 1,
            $modal.' submits zero when unchecked');
        tableCheck($xpath->query('//label[@for="table_allow_multiple_orders"]')->length === 1
            && str_contains($rendered, 'Allow multiple orders'), $modal.' renders the associated readable label');
    }
}
Mockery::close();
echo "PASS: {$checks} table settings checks (SQLite memory and rendered modals only).\n";
