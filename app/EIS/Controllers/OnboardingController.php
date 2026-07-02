<?php

namespace App\EIS\Controllers;

use Illuminate\Http\Request;
use App\EIS\Services\Onboarding\ActivationService;
use App\Http\Controllers\Controller;

class OnboardingController extends Controller
{
    public function __construct(
        protected ActivationService $service
    ) {}

    /**
     * Request activation
     */
    public function activate(Request $request)
    {
        return response()->json(
            $this->service->requestActivation($request->all())
        );
    }

    /**
     * Confirm activation
     */
    public function confirm(Request $request)
    {
        return response()->json(
            $this->service->confirmActivation(
                $request->activationCode
            )
        );
    }

    /**
     * Check status
     */
    public function status()
    {
        return response()->json(
            $this->service->status()
        );
    }
}