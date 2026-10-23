<?php

namespace Modules\EIS\Http\Middleware;

use Closure;

class EnforceTerminalBusiness
{
    public function handle($request, Closure $next)
    {
        // Webhook authentication is a separate EIS machine-to-machine contract.
        if ($request->is('api/v1/webhooks/*')) {
            return $next($request);
        }
        $user = $request->user();
        if (!$user && $request->is('api/*')) {
            $user = auth('api')->user();
            if ($user) {
                auth()->shouldUse('api');
            }
        }
        abort_unless($user, 401, 'EIS access requires an authenticated business account.');
        $businessId = $user->business_id;
        abort_unless($businessId, 403, 'EIS access requires a business terminal.');
        $targets = [$request->route('businessId'), $request->input('business_id')];
        $bulk = $request->input('business_ids', []);
        abort_unless(is_array($bulk), 422, 'Business IDs must be an array.');
        foreach (array_merge($targets, $bulk) as $target) {
            if ($target !== null && (!is_scalar($target) || (string) $target !== (string) $businessId)) {
                abort(403, 'EIS access is limited to your business terminal.');
            }
        }
        if ($request->hasSession() && $request->session()->has('user.business_id')) {
            abort_unless((string) $request->session()->get('user.business_id') === (string) $businessId,
                403, 'EIS session does not match the authenticated business terminal.');
        }
        return $next($request);
    }
}
