<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CleanupPastAvailabilities;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendamento da tarefa de limpeza de disponibilidades passadas a cada minuto
Schedule::command(CleanupPastAvailabilities::class)->everyMinute();
