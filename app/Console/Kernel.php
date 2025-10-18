<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run appointment reminders every 10 minutes
        $schedule->command('appointments:send-reminders')
                 ->everyTenMinutes()
                 ->withoutOverlapping(5) // Prevent overlaps with 5 min expiry
                 ->runInBackground()
                 ->onSuccess(function () {
                     \Log::info('Appointment reminders executed successfully');
                 })
                 ->onFailure(function () {
                     \Log::error('Appointment reminders failed');
                 });
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}