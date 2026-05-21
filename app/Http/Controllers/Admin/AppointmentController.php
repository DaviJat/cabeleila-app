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
    /**
     * Store a newly created appointment or update the status of an existing one.
     *
     * @param AppointmentRequest $request
     * @return RedirectResponse
     */
    public function store(AppointmentRequest $request): RedirectResponse
    {
        try {
            // Use a database transaction to protect multi-table operational integrity
            DB::transaction(function () use ($request) {

                // Check if we are updating an existing appointment or creating a new one
                if ($request->filled('id')) {
                    // Update the status of an existing appointment
                    $appointment = Appointment::findOrFail($request->input('id'));

                    $appointment->update([
                        'status' => $request->input('status')
                    ]);

                    // Free up the availability slot immediately if the appointment gets canceled
                    if ($request->input('status') === 'canceled') {
                        $appointment->availability()->update([
                            'is_available' => true
                        ]);
                    }
                } else {
                    // Create a new appointment with the provided availability, client, and notes
                    $appointment = Appointment::create([
                        'availability_id' => $request->input('availability_id'),
                        'client_id'       => $request->input('client_id'),
                        'notes'           => $request->input('notes'),
                        'status'          => 'confirmed', // Manually added bookings start as confirmed
                    ]);

                    // Attach chosen services array to the pivot table
                    $appointment->services()->attach($request->input('service_ids'));

                    // Block the availability slot to prevent double-booking
                    $availability = Availability::find($request->input('availability_id'));
                    $availability->update([
                        'is_available' => false
                    ]);
                }
            });

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Appointment store failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Ocorreu um erro ao processar a requisição.');
        }
    }
}
