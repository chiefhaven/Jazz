<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testConfiguredAdminUsernamesAreReturned()
    {
        config()->set('constants.administrator_usernames', 'haven, thejazz,  admin');

        $controller = app(\Modules\Connector\Http\Controllers\Api\UserController::class);
        $response = $controller->adminUsernames();

        $this->assertSame(['haven', 'thejazz', 'admin'], $response['data']['usernames']);
    }

    public function testNewSellDoesNotCrashWhenOnlyOverduePermissionIsGranted()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->forceFill(['id' => 1, 'business_id' => 1, 'name' => 'Test User']);
        $user->shouldReceive('hasRole')->withAnyArgs()->andReturn(false);
        $user->shouldReceive('hasAnyPermission')->withAnyArgs()->andReturnUsing(function ($permissions) {
            $permissions = is_array($permissions) ? $permissions : [$permissions];

            return in_array('sell.view', $permissions, true) || in_array('direct_sell.access', $permissions, true);
        });
        $user->shouldReceive('permitted_locations')->with(1)->andReturn('all');
        $user->shouldReceive('can')->withAnyArgs()->andReturnUsing(function ($permission) {
            return $permission === 'view_overdue_sells_only';
        });

        Auth::shouldReceive('user')->andReturn($user);

        $request = Request::create('/connector/api/new_sell', 'GET', ['per_page' => 1]);
        $request->setUserResolver(fn () => $user);

        $controller = app(\Modules\Connector\Http\Controllers\Api\ProductSellController::class);
        $response = $controller->newSell();

        $this->assertNotNull($response);
        $this->assertIsObject($response);
    }
}
