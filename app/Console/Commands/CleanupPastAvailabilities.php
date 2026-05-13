<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Models\Availability;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

#[Signature('app:cleanup-past-availabilities')]
#[Description('Atualiza status de agendamentos passados e bloqueia horários expirados')]
class CleanupPastAvailabilities extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Reutiliza a lógica de verificação de horários passados
        $pastTimeRule = function ($query) use ($now) {
            $query->where('date', '<', $now->toDateString())
                ->orWhere(function ($q) use ($now) {
                    $q->where('date', '=', $now->toDateString())
                        ->where('hour', '<', $now->toTimeString());
                });
        };

        // Passo 1: Atualiza agendamentos "pendentes" em horários que já passaram para "cancelados"
        $canceledCount = Appointment::where('status', 'pending')
            ->whereHas('availability', $pastTimeRule)
            ->update(['status' => 'canceled']);

        // Passo 2: Atualiza agendamentos "confirmados" em horários que já passaram para "finalizados"
        $completedCount = Appointment::where('status', 'confirmed')
            ->whereHas('availability', $pastTimeRule)
            ->update(['status' => 'completed']);

        // Passo 3: Garante que qualquer horário passado sem agendamento seja bloqueado
        $affectedSlots = Availability::where('is_available', true)
            ->where($pastTimeRule)
            ->update(['is_available' => false]);

        // Feedback detalhado para o terminal
        $this->info("Sucesso! Resumo da limpeza:");
        $this->line("- {$canceledCount} agendamentos pendentes foram cancelados.");
        $this->line("- {$completedCount} agendamentos confirmados foram finalizados.");
        $this->line("- {$affectedSlots} horários expirados foram desativados.");
    }
}
