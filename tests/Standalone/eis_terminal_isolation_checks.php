<?php

// Reuse the isolated catalog fixture; never boot the application or contact EIS.
require __DIR__.'\eis_product_sync_checks.php';

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Configuration\ConfigurationSyncService;
use Modules\EIS\Services\Configuration\EISConfigurationResponse;
use Modules\EIS\Services\Configuration\EisConfigurationClient;
use Modules\EIS\Services\Terminal\TerminalContext;

[$db, $client, $sync] = catalogFixture();
$app = Illuminate\Container\Container::getInstance();
$checks = 0;
function terminalReject(callable $work, string $message): void
{
    try {
        $work();
    } catch (InvalidArgumentException $e) {
        catalogCheck(true, $message);
        return;
    }
    throw new RuntimeException($message.' should be rejected');
}
$db->getSchemaBuilder()->table('eis_settings', function (Blueprint $table) {
    foreach (['jwt_token', 'tpin', 'device_id', 'secret_key'] as $column) {
        $table->text($column)->nullable();
    }
});
$tokenA = str_repeat('a', 24);
$tokenB = str_repeat('b', 24);
$db->table('eis_settings')->where('business_id', 1)->update([
    'jwt_token' => $tokenA, 'tpin' => 'TIN-A', 'device_id' => 'TERMINAL-A', 'secret_key' => 'secret-a',
]);
$db->table('eis_settings')->insert([
    'business_id' => 2, 'jwt_token' => $tokenB, 'tpin' => 'TIN-B', 'device_id' => 'TERMINAL-B',
    'branch_id' => 'SITE-B', 'secret_key' => 'secret-b',
]);
$settings = TerminalContext::settings(1, $tokenA);
catalogCheck(TerminalContext::site($settings) === 'SITE-A', 'Business terminal resolves its own branch');
terminalReject(fn () => TerminalContext::settings(1, $tokenB), 'Another terminal token cannot sync this business');
terminalReject(fn () => TerminalContext::assertBusiness($settings, 2), 'Cross-business settings are rejected');
terminalReject(fn () => $sync->sync((object) ['business_id' => 1, 'site_id' => 'SITE-B'], 1),
    'Product sync cannot use a different terminal site within the same business');
catalogCheck($client->requests === [], 'Wrong-terminal product request fails before HTTP');
terminalReject(fn () => (new Modules\EIS\Services\Products\ProductUpsertService())->upsert(1, ['site_id' => 'SITE-B'], 'FOREIGN'),
    'Direct product upserts cannot bypass terminal ownership');
terminalReject(fn () => TerminalContext::site((object) ['device_id' => 'DEVICE']), 'Device cannot masquerade as site');
terminalReject(fn () => TerminalContext::site((object) ['branch_id' => '']), 'No default site is invented');
catalogCheck(TerminalContext::site((object) ['site_id' => 'SITE-X', 'branch_id' => 'SITE-A']) === 'SITE-X',
    'All EIS requests share explicit-site precedence');
$data = (object) [
    'globalConfiguration' => (object) ['versionNo' => 1],
    'terminalConfiguration' => (object) ['versionNo' => 1, 'terminalId' => 'TERMINAL-A',
        'terminalSite' => (object) ['siteId' => 'SITE-A']],
    'taxpayerConfiguration' => (object) ['versionNo' => 1, 'tin' => 'TIN-A'],
];
TerminalContext::assertConfiguration($settings, $data);
catalogCheck(true, 'Matching terminal configuration is accepted');
foreach (['site', 'tin', 'terminal'] as $change) {
    $wrong = unserialize(serialize($data));
    if ($change === 'site') {
        $wrong->terminalConfiguration->terminalSite->siteId = 'SITE-B';
    } elseif ($change === 'tin') {
        $wrong->taxpayerConfiguration->tin = 'TIN-B';
    } else {
        $wrong->terminalConfiguration->terminalId = 'TERMINAL-B';
    }
    terminalReject(fn () => TerminalContext::assertConfiguration($settings, $wrong), 'Foreign '.$change.' configuration rejected');
}
$db->table('eis_settings')->insert(['business_id' => 1, 'branch_id' => 'OTHER']);
terminalReject(fn () => TerminalContext::settings(1), 'Ambiguous settings never select an arbitrary terminal');
$db->table('eis_settings')->where('business_id', 1)->where('branch_id', 'OTHER')->delete();

// Authenticate internal endpoints independently of body/query/route business IDs.
$middleware = new Modules\EIS\Http\Middleware\EnforceTerminalBusiness();
$user = new App\User();
$user->business_id = 1;
function terminalRequest(string $path, array $body = [], $routeBusiness = null): Request
{
    global $user;
    $request = Request::create('http://localhost/'.$path, 'POST', $body);
    $request->setUserResolver(fn () => $user);
    $route = new Illuminate\Routing\Route('POST', 'fixture/{businessId?}', fn () => null);
    $route->bind(Request::create('http://localhost/fixture'.($routeBusiness === null ? '' : '/'.$routeBusiness)));
    $request->setRouteResolver(fn () => $route);
    return $request;
}
catalogCheck($middleware->handle(terminalRequest('api/v1/terminal/activate', ['business_id' => 1]), fn () => true),
    'Same-business terminal request passes');
foreach ([
    terminalRequest('api/v1/terminal/activate', ['business_id' => 2]),
    terminalRequest('api/v1/terminal/details/2', [], 2),
    terminalRequest('api/v1/terminal/bulk-activate', ['business_ids' => [1, 2]]),
    terminalRequest('api/v1/public/configuration/2', [], 2),
] as $request) {
    try {
        $middleware->handle($request, fn () => true);
        throw new RuntimeException('Expected cross-terminal HTTP rejection');
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        catalogCheck($e->getStatusCode() === 403, 'Foreign route, body and bulk targets are forbidden');
    }
}
$request = terminalRequest('eis/terminal/activate');
$request->setUserResolver(fn () => null);
try {
    $middleware->handle($request, fn () => true);
    throw new RuntimeException('Expected anonymous internal rejection');
} catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
    catalogCheck($e->getStatusCode() === 401, 'Internal terminal endpoints require an account');
}
$request = terminalRequest('api/v1/webhooks/configuration-update', ['business_id' => 2]);
$request->setUserResolver(fn () => null);
catalogCheck($middleware->handle($request, fn () => true), 'Webhook authentication remains unchanged as requested');
$session = new Illuminate\Session\Store('terminal', new Illuminate\Session\ArraySessionHandler(120));
$session->put('user.business_id', 2);
$request = terminalRequest('eis/terminal/status');
$request->setLaravelSession($session);
try {
    $middleware->handle($request, fn () => true);
    throw new RuntimeException('Expected session mismatch rejection');
} catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
    catalogCheck($e->getStatusCode() === 403, 'Session cannot select another account terminal');
}

// Wrong credentials must fail before a request, and wrong response identity before writes.
$configurationClient = new class extends EisConfigurationClient {
    public int $calls = 0;
    public object $data;
    public function latest(string $token): EISConfigurationResponse {
        ++$this->calls;
        return new EISConfigurationResponse((object) ['statusCode' => 1, 'data' => $this->data]);
    }
};
$validator = Mockery::mock(Modules\EIS\Services\Configuration\Validators\ConfigurationValidator::class);
$configurationService = new ConfigurationSyncService($configurationClient, $validator);
$configurationClient->data = $data;
try {
    $configurationService->sync(1, $tokenB);
    throw new RuntimeException('Expected mismatched token rejection');
} catch (Modules\EIS\Exceptions\SyncException $e) {
    catalogCheck($configurationClient->calls === 0, 'Foreign token rejected before configuration HTTP call');
}
$db->getSchemaBuilder()->create('eis_configurations', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('business_id');
    $table->integer('global_version')->nullable();
    $table->integer('terminal_version')->nullable();
    $table->integer('taxpayer_version')->nullable();
    $table->string('tpin')->nullable();
    $table->boolean('is_vat_registered')->default(false);
    $table->text('raw_response')->nullable();
    $table->timestamp('last_synced_at')->nullable();
    $table->timestamps();
});
$db->table('eis_configurations')->insert([
    'business_id' => 1, 'global_version' => 1, 'terminal_version' => 1, 'taxpayer_version' => 1, 'tpin' => 'TIN-A',
    'raw_response' => json_encode(['data' => $data]),
]);
$configurationClient->data = unserialize(serialize($data));
$configurationClient->data->terminalConfiguration->terminalSite->siteId = 'SITE-B';
$before = $db->table('eis_configurations')->get()->toJson();
foreach ([false, true] as $force) {
    try {
        $force ? $configurationService->forceSync(1, $tokenA) : $configurationService->sync(1, $tokenA);
        throw new RuntimeException('Expected foreign response rejection');
    } catch (Modules\EIS\Exceptions\SyncException $e) {
        catalogCheck(str_contains($e->getMessage(), 'site and TIN'), 'Configuration identity checked even when versions unchanged');
        catalogCheck($db->table('eis_configurations')->get()->toJson() === $before,
            'Rejected normal/forced configuration leaves committed identity intact');
    }
}
$configurationClient->data = $data;
$result = $configurationService->sync(1, $tokenA);
catalogCheck((int) $result->business_id === 1, 'Valid unchanged configuration still syncs');

// Activation must reject a terminal already assigned to another business before settings writes.
$db->getSchemaBuilder()->create('eis_terminal_configurations', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('configuration_id');
    $table->string('terminal_id');
});
$activation = (new ReflectionClass(Modules\EIS\Services\Terminal\EisTerminalActivationService::class))->newInstanceWithoutConstructor();
$process = new ReflectionMethod($activation, 'processActivationResponse');
$process->setAccessible(true);
foreach (['TERMINAL-B', 'UNRELATED'] as $terminalId) {
    $response = (object) ['data' => (object) ['activatedTerminal' => (object) ['terminalId' => $terminalId], 'configuration' => $data]];
    terminalReject(fn () => $process->invoke($activation, 1, $response, 'CODE', [], 1),
        'Activation cannot steal another account terminal or replace existing identity');
}
catalogCheck(TerminalContext::settings(1)->device_id === 'TERMINAL-A', 'Activation rejection preserves terminal assignment');
$db->table('eis_terminal_configurations')->insert([
    'configuration_id' => $db->table('eis_configurations')->where('business_id', 1)->value('id'),
    'terminal_id' => 'TERMINAL-A',
]);
$db->getSchemaBuilder()->table('eis_settings', function (Blueprint $table) {
   foreach (['last_sync_at', 'sync_status', 'sync_error'] as $column) {
       $table->text($column)->nullable();
   }
});
$db->getSchemaBuilder()->table('eis_configurations', function (Blueprint $table) {
   foreach (['tax_office_code', 'tax_office_name'] as $column) {
       $table->text($column)->nullable();
   }
});
$db->getSchemaBuilder()->table('eis_terminal_configurations', function (Blueprint $table) {
   foreach ([
       'version', 'terminal_label', 'is_active', 'is_confirmed', 'email_address', 'phone_number',
       'trading_name', 'max_transaction_age', 'max_cummulative_amount', 'address_lines', 'raw_data',
       'last_synced_at', 'activated_at', 'activated_by', 'deactivated_at', 'deactivated_by',
       'deactivation_reason', 'toggled_at', 'toggled_by', 'activation_code', 'activation_environment',
       'terminal_position', 'taxpayer_id', 'activation_date', 'jwt_token', 'secret_key', 'created_at', 'updated_at',
   ] as $column) {
       $table->text($column)->nullable();
   }
});
foreach (['eis_terminal_sites', 'eis_offline_limits'] as $tableName) {
   $db->getSchemaBuilder()->create($tableName, function (Blueprint $table) {
       $table->increments('id');
       $table->integer('terminal_configuration_id');
       $table->timestamps();
   });
}
$db->table('eis_settings')->where('business_id', 1)->update(['device_id' => null]);
$replacement = unserialize(serialize($data));
$replacement->terminalConfiguration->terminalId = 'TERMINAL-C';
$replacement->terminalConfiguration->terminalSite->siteId = 'SITE-C';
$replacement->taxpayerConfiguration->tin = 'TIN-C';
$response = (object) ['data' => (object) [
   'activatedTerminal' => (object) [
       'terminalId' => 'TERMINAL-C',
       'terminalPosition' => 3,
       'taxpayerId' => 300,
       'terminalCredentials' => (object) ['jwtToken' => 'token-c', 'secretKey' => 'secret-c'],
   ],
   'configuration' => $replacement,
]];
$replacementResult = $process->invoke($activation, 1, $response, 'CODE-C', [], 1);
catalogCheck($replacementResult['success'], 'A new terminal can replace the existing business terminal');
catalogCheck($db->table('eis_settings')->where('business_id', 1)->value('device_id') === 'TERMINAL-C',
   'Terminal replacement updates the business settings');
catalogCheck($db->table('eis_terminal_configurations')->where('configuration_id',
   $db->table('eis_configurations')->where('business_id', 1)->value('id'))->count() === 1
   && $db->table('eis_terminal_configurations')->value('terminal_id') === 'TERMINAL-C',
   'Terminal replacement updates the existing configuration instead of creating a second terminal');
catalogCheck($db->table('eis_configurations')->where('business_id', 1)->value('tpin') === 'TIN-C',
   'Terminal replacement updates the business configuration identity');
$db->table('eis_settings')->where('business_id', 1)->update([
   'device_id' => 'TERMINAL-A', 'branch_id' => 'SITE-A', 'tpin' => 'TIN-A',
]);
$db->table('eis_configurations')->where('business_id', 1)->update([
   'tpin' => 'TIN-A', 'raw_response' => json_encode(['data' => $data]),
]);
$db->table('eis_terminal_configurations')->where('configuration_id',
   $db->table('eis_configurations')->where('business_id', 1)->value('id'))->update(['terminal_id' => 'TERMINAL-A']);

// Location reconciliation must target the terminal site, not the first business location.
$db->getSchemaBuilder()->table('business', function (Blueprint $table) {
    foreach (['name', 'tax_number_1', 'tax_label_1', 'enable_inline_tax'] as $column) {
        $table->text($column)->nullable();
    }
});
$db->getSchemaBuilder()->table('business_locations', function (Blueprint $table) {
    foreach (['country', 'state', 'city', 'zip_code', 'invoice_scheme_id', 'invoice_layout_id',
        'sale_invoice_scheme_id', 'mobile', 'email'] as $column) {
        $table->text($column)->nullable();
    }
});
foreach (['invoice_schemes', 'invoice_layouts'] as $name) {
    $db->getSchemaBuilder()->create($name, function (Blueprint $table) {
        $table->increments('id');
        $table->integer('business_id');
        $table->string('name');
        $table->boolean('is_default')->default(true);
    });
    $db->table($name)->insert(['business_id' => 1, 'name' => 'Default']);
}
$db->table('business_locations')->where('id', 1)->update(['eis_site_id' => 'LEGACY', 'name' => 'Legacy location']);
$db->table('business_locations')->insert(['id' => 3, 'business_id' => 1, 'eis_site_id' => 'SITE-A', 'name' => 'Terminal']);
$data->terminalConfiguration->terminalSite->siteName = 'Terminal updated';
$db->table('eis_configurations')->where('business_id', 1)->update(['raw_response' => json_encode(['data' => $data])]);
$location = ConfigurationSyncService::businessLocation(1);
catalogCheck((int) $location->id === 3 && $location->name === 'Terminal updated',
    'Location sync updates the terminal mapping and retains its ID');
catalogCheck($db->table('business_locations')->where('id', 1)->value('eis_site_id') === 'LEGACY',
    'Location sync does not overwrite an arbitrary legacy business location');
catalogCheck($db->table('business_locations')->where('business_id', 1)->count() === 2,
    'Repeated terminal reconciliation does not duplicate mapped locations');
$db->table('business_locations')->insert(['business_id' => 1, 'eis_site_id' => 'SITE-A', 'name' => 'Duplicate']);
terminalReject(fn () => ConfigurationSyncService::businessLocation(1),
    'Duplicate terminal mappings fail rather than update an arbitrary location');
Mockery::close();
echo "PASS: {$checks} terminal isolation checks; in-memory fixtures only.\n";
