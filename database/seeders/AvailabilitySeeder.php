<?php

namespace Database\Seeders;

use App\Models\Availability;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    // Generates schedule for the current and next month. 
    // All slots start as available, as past slots will be handled by the scheduled task.
    public function run(): void
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->addMonth()->endOfMonth();

        while ($startDate->lte($endDate)) {

            // Rule 1: Closed on Sundays
            if ($startDate->isSunday()) {
                $startDate->addDay();
                continue;
            }

            // Rule 2: Define available hours based on the day of the week
            if ($startDate->isSaturday()) {
                // Saturdays: Morning only (up to 12:00)
                $hours = [8, 9, 10, 11];
            } else {
                // Weekdays: Excludes 12:00 and 13:00 for lunch break
                $hours = [8, 9, 10, 11, 14, 15, 16, 17];
            }

            // Loop through the permitted hours
            foreach ($hours as $hour) {
                $timeString = sprintf('%02d:00:00', $hour);

                Availability::factory()->create([
                    'date' => $startDate->format('Y-m-d'),
                    'hour' => $timeString,
                    'is_available' => true,
                ]);
            }

            $startDate->addDay();
        }
    }
}
