<?php

namespace App\EIS\Http\Middleware;

use Closure;
use App\EIS\Services\Authentication\AuthenticationService;

class EnsureEisToken
{
    public function __construct(
        protected AuthenticationService $auth
    ) {}

    public function handle($request, Closure $next)
    {
        try {
            $this->auth->getToken();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'EIS authentication failed',
                'error' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}