<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Jobs\SubmitSaleJob;
use Modules\EIS\Jobs\SyncEISConfigurationJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $env = config('app.env');
        $email = config('mail.username');

        if ($env === 'live') {
            //Scheduling backup, specify the time when the backup will get cleaned & time when it will run.
            
            $schedule->command('backup:clean')->daily()->at('01:00');
            $schedule->command('backup:run')->daily()->at('01:30');


            //Schedule to create recurring invoices
            $schedule->command('pos:generateSubscriptionInvoices')->dailyAt('23:30');
            $schedule->command('pos:updateRewardPoints')->dailyAt('23:45');

            $schedule->command('pos:autoSendPaymentReminder')->dailyAt('8:00');

            $schedule->command('pos:generateRecurringExpense')->dailyAt('02:00');

            // Schedule the EIS configuration sync job
            $schedule->job(new SyncEISConfigurationJob())
                ->everyMinute()
                ->name('eis-configuration-sync')
                ->withoutOverlapping(300)
                ->onFailure(function () {
                    Log::error('EIS Configuration Sync Job Scheduler Failure');
            });
            
             // Dispatch all unsubmitted sales every 15 minutes
            $schedule->command('eis:dispatch-unsubmitted')
                ->everyFifteenMinutes()
                ->withoutOverlapping(300)
                ->runInBackground();

            // Retry failed transactions every hour
            $schedule->command('eis:retry-failed-transactions')
                ->hourly()
                ->withoutOverlapping(300)
                ->runInBackground();    
                }

        if ($env === 'demo') {
            //IMPORTANT NOTE: This command will delete all business details and create dummy business, run only in demo server.
            $schedule->command('pos:dummyBusiness')
                    ->cron('0 */3 * * *')
                    //->everyThirtyMinutes()
                    ->emailOutputTo($email);
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
