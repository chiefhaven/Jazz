<?php

namespace App\EIS\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EisEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\EIS\Events\InvoiceSubmitted::class => [
            \App\EIS\Listeners\LogInvoiceSubmitted::class,
        ],
        \App\EIS\Events\InvoiceFailed::class => [
            \App\EIS\Listeners\LogInvoiceFailed::class,
        ],
        \App\EIS\Events\TokenExpired::class => [
            \App\EIS\Listeners\LogTokenExpired::class,
        ],
        \App\EIS\Events\TerminalActivated::class => [
            \App\EIS\Listeners\LogTerminalActivated::class,
        ],
    ];
}