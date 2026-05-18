<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;
    protected $businessUtil;
    protected $moduleUtil;

    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Override username to use 'password' (PIN) as the only identifier.
     */
    public function username()
    {
        return 'password';
    }

    /**
     * Override validation to handle only the PIN (password field).
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);
    }

    /**
     * Override the attempt logic. 
     * In a PIN-only system, we must check every user's hash until we find a match.
     */
    protected function attemptLogin(Request $request)
    {
        // Get all potential users (you can filter by business_id if needed)
        $users = User::all();

        foreach ($users as $user) {
            // Check if the entered PIN matches the hashed password in the DB
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user, $request->filled('remember'));
                return true;
            }
        }

        return false;
    }

    public function logout()
    {
        $this->businessUtil->activityLog(auth()->user(), 'logout');
        request()->session()->flush();
        Auth::logout();
        return redirect('/login');
    }

    protected function authenticated(Request $request, $user)
    {
        $this->businessUtil->activityLog($user, 'login', null, [], false, $user->business_id);

        if (!$user->business->is_active) {
            Auth::logout();
            return redirect('/login')->with('status', ['success' => 0, 'msg' => __('lang_v1.business_inactive')]);
        } elseif ($user->status != 'active') {
            Auth::logout();
            return redirect('/login')->with('status', ['success' => 0, 'msg' => __('lang_v1.user_inactive')]);
        } elseif (!$user->allow_login) {
            Auth::logout();
            return redirect('/login')->with('status', ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]);
        }
    }

    protected function redirectTo()
    {
        $user = Auth::user();
        if (!$user->can('dashboard.data') && $user->can('sell.create')) {
            return '/pos/create';
        }
        return '/home';
    }
}
