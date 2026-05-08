<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Console\Command;

#[Signature('app:cleanup-past-availabilities')]
#[Description('Command description')]
class CleanupPastAvailabilities extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Desativa todas as disponibilidades que estão no passado, considerando tanto a data quanto a hora
        $affected = Availability::where('is_available', true)
            ->where(function ($query) use ($now) {
                $query->where('date', '<', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', '=', $now->toDateString())
                            ->where('hour', '<', $now->toTimeString());
                    });
            })
            ->update(['is_available' => false]);

        $this->info("Sucesso: {$affected} horários expirados foram desativados.");
    }
}
