<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('payments:reject-expired')->everyMinute();
        });
    }
}
