<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Availability;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class AppointmentController extends Controller
{
    /**
     * Store a newly created appointment or update the status of an existing one.
     * Generates a deep-linked WhatsApp URL payload to dispatch back to the dashboard state.
     *
     * @param AppointmentRequest $request
     * @param WhatsAppService $whatsAppService
     * @return RedirectResponse
     */
    public function store(AppointmentRequest $request, WhatsAppService $whatsAppService): RedirectResponse
    {
        $whatsAppRedirectUrl = null;

        try {
            // Use a database transaction to protect multi-table operational integrity
            DB::transaction(function () use ($request, $whatsAppService, &$whatsAppRedirectUrl) {

                if ($request->filled('id')) {
                    // Update the status of an existing appointment
                    $appointment = Appointment::with(['client', 'availability'])->findOrFail($request->input('id'));
                    $status = $request->input('status');

                    $appointment->update([
                        'status' => $status
                    ]);

                    // Free up the availability slot immediately if the appointment gets canceled
                    if ($status === 'canceled') {
                        $appointment->availability()->update([
                            'is_available' => true
                        ]);
                    }

                    // Generate the WhatsApp Web URL for status updates
                    $whatsAppRedirectUrl = $whatsAppService->getStatusNotificationUrl($appointment->client, $appointment, $status);
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

                    // Load relations fresh to build the WhatsApp notification correctly
                    $appointment->load(['client', 'availability', 'services']);

                    // Generate the WhatsApp Web URL for newly created appointments
                    $whatsAppRedirectUrl = $whatsAppService->getAdminActionNotificationUrl($appointment->client, $appointment, true);
                }
            });

            // Pass the URL string back to the Inertia frontend using transient session flashes
            if ($whatsAppRedirectUrl) {
                return redirect()->back()->with('whatsapp_url', $whatsAppRedirectUrl);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Appointment store failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Ocorreu um erro ao processar a requisição.');
        }
    }
}
