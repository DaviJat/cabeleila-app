<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule a command to clean up past events every minute (for testing purposes, adjust as needed for production)
Schedule::command('app:cleanup-past-events')->everyMinute();
