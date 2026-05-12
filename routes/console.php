<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CleanupPastAvailabilities;
use App\Models\Availability;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CleanupPastAvailabilities::class)->everyMinute();

Schedule::call(function () {
    $now = Carbon::now();

    Availability::where('is_available', true)
        ->where(function ($query) use ($now) {
            // Regra 1: Datas anteriores a hoje
            $query->where('date', '<', $now->toDateString())
                // Regra 2: É hoje, mas a hora já passou
                ->orWhere(function ($q) use ($now) {
                    $q->where('date', '=', $now->toDateString())
                        ->where('hour', '<', $now->toTimeString());
                });
        })
        ->update(['is_available' => false]);
})->everyMinute();
