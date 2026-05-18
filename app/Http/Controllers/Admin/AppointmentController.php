<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Availability;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class AppointmentController extends Controller
{
    // Store a newly created appointment or update status of an existing one.
    public function store(AppointmentRequest $request): RedirectResponse
    {
        try {
            // Use a database transaction to protect multi-table operational integrity
            DB::transaction(function () use ($request) {

                // Check if we're updating an existing appointment or creating a new one
                if ($request->filled('id')) {
                    // A: Update the status of an existing appointment
                    $appointment = Appointment::findOrFail($request->id);

                    $appointment->update([
                        'status' => $request->status
                    ]);

                    // Free up the availability slot immediately if the appointment gets canceled
                    if ($request->status === 'canceled') {
                        $appointment->availability()->update([
                            'is_available' => true
                        ]);
                    }
                } else {
                    // B: Create a new appointment with the provided availability, client, and notes
                    $appointment = Appointment::create([
                        'availability_id' => $request->availability_id,
                        'client_id'       => $request->client_id,
                        'notes'           => $request->notes,
                        'status'          => 'confirmed', // Manually added bookings start as confirmed
                    ]);

                    // Attach chosen services array to the pivot table
                    $appointment->services()->attach($request->service_ids);

                    // Block the availability slot from being re-selected
                    $availability = Availability::find($request->availability_id);
                    $availability->update([
                        'is_available' => false
                    ]);
                }
            });

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro ao processar a requisição.');
        }
    }
}
