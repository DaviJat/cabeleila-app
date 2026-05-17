<?php

namespace App\Console\Commands;

use App\Models\Availability;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('app:cleanup-past-events')]
#[Description('Automates the status of past availabilities and appointments')]
class CleanupPastAvailabilities extends Command
{
    public function handle()
    {
        $now = Carbon::now();

        // Chronological rule to identify what is in the past
        $pastCondition = function ($query) use ($now) {
            $query->where('date', '<', $now->toDateString())
                ->orWhere(function ($q) use ($now) {
                    $q->where('date', '=', $now->toDateString())
                        ->where('hour', '<', $now->toTimeString());
                });
        };

        // 1. Pending becomes Canceled (Expired)
        $canceled = Appointment::where('status', 'pending')
            ->whereHas('availability', $pastCondition)
            ->update(['status' => 'canceled']);

        // 2. Confirmed becomes Completed automatically
        $realized = Appointment::where('status', 'confirmed')
            ->whereHas('availability', $pastCondition)
            ->update(['status' => 'completed']);

        // 3. Past free slots are blocked
        $disabledSlots = Availability::where('is_available', true)
            ->where($pastCondition)
            ->update(['is_available' => false]);

        // Feedback in the control panel/logs
        $this->info("Cleanup completed successfully!");
        $this->line("- {$canceled} pending appointments were expired/canceled.");
        $this->line("- {$realized} confirmed appointments were marked as completed.");
        $this->line("- {$disabledSlots} empty slots were disabled.");
    }
}
