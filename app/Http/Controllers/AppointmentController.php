<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Availability;
use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Display the scheduling form or preparation data for editing an existing appointment.
     *
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function create(Request $request)
    {
        $services = Service::all();

        // Fetch only available slots from today onward; if today, filter out past hours
        $availabilities = Availability::where('is_available', true)
            ->where(function ($query) {
                $query->where('date', '>', Carbon::today())
                    ->orWhere(function ($q) {
                        $q->where('date', '=', Carbon::today())
                            ->where('hour', '>', Carbon::now()->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('hour')
            ->get();

        // Group available slots by date string and format time components to "HH:MM"
        $groupedSlots = $availabilities->groupBy(function ($item) {
            return $item->date->format('Y-m-d');
        })->map(function ($group) {
            return $group->pluck('hour')->map(fn($time) => substr($time, 0, 5));
        });

        $loggedClient = Auth::guard('clients')->user();
        $editingAppointment = null;
        $clientUpcomingAppointments = [];

        if ($loggedClient) {
            // If an edit identifier is present in the query, fetch the record for update validation
            if ($request->has('edit_id')) {
                $editingAppointment = Appointment::with(['services', 'availability'])
                    ->where('client_id', $loggedClient->id)
                    ->findOrFail($request->input('edit_id'));

                // Normalize date component extraction to safeguard against varied data models
                $availability = $editingAppointment->availability;
                $datePart = $availability->date;

                if (is_object($datePart) && method_exists($datePart, 'format')) {
                    $dateString = $datePart->format('Y-m-d');
                } else {
                    $dateString = substr((string) $datePart, 0, 10);
                }

                $hourPart = (string) $availability->hour;
                $hourString = substr($hourPart, 0, 8);

                $appointmentDateTime = Carbon::parse($dateString . ' ' . $hourString);

                // Enforce a strict business rule: block alterations within 48 hours of the scheduled time
                if (now()->diffInHours($appointmentDateTime, false) < 48) {
                    return redirect()->route('clients.appointments');
                }
            }

            // Retrieve upcoming commitments to prevent schedule duplications within the calendar interface
            $clientUpcomingAppointments = Appointment::with(['services', 'availability'])
                ->where('client_id', $loggedClient->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereHas('availability', function ($query) {
                    $query->where('date', '>=', Carbon::today());
                })
                ->get();
        }

        return Inertia::render('Appointment/Schedule', [
            'dbServices'                 => $services,
            'availableSlots'             => $groupedSlots,
            'loggedClient'               => $loggedClient,
            'editingAppointment'         => $editingAppointment,
            'clientUpcomingAppointments' => $clientUpcomingAppointments,
            'flash' => [
                'success' => session('success')
            ]
        ]);
    }

    /**
     * Store a newly created appointment or authenticate client using passwordless OTP credentials.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string',
            'whatsapp' => 'required|string',
            'date'     => 'required|date',
            'time'     => 'required',
            'services' => 'required|array|min:1',
        ]);

        // If client is logged in use their instance, otherwise validate OTP token for transient sign-in
        if (Auth::guard('clients')->check()) {
            $client = Auth::guard('clients')->user();
        } else {
            $request->validate(['otp' => 'required|string']);

            $client = Client::where('phone', $request->input('whatsapp'))
                ->where('otp_code', $request->input('otp'))
                ->where('otp_expires_at', '>', now())
                ->first();

            if (! $client) {
                return back()->withErrors(['otp' => 'Código inválido ou expirado. Tente novamente.']);
            }

            $client->update([
                'full_name' => $request->input('name'),
                'otp_code'  => null,
            ]);

            Auth::guard('clients')->login($client, true);
        }

        // Lock down the chosen availability slot, making sure it hasn't been sniped by another transaction
        $availability = Availability::where('date', $request->input('date'))
            ->where('hour', $request->input('time') . ':00')
            ->where('is_available', true)
            ->firstOrFail();

        // Instantiate the core appointment log mapping relations
        $appointment = Appointment::create([
            'client_id'       => $client->id,
            'availability_id' => $availability->id,
            'status'          => 'pending',
            'notes'           => 'Agendamento via site',
        ]);

        // Extract selected service entity keys and sync attachment pivot table properties
        $serviceIds = collect($request->input('services'))->pluck('id');
        $appointment->services()->attach($serviceIds);

        // Revoke the slot availability flags to prevent double-booking collisions
        $availability->update(['is_available' => false]);

        return redirect()->route('agendar')->with('success', 'Agendamento confirmado com sucesso!');
    }

    /**
     * Update an existing appointment data context, releasing old resources back to the open pool.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'date'     => 'required|date',
            'time'     => 'required',
            'services' => 'required|array|min:1',
        ]);

        // Assert that the record exists and belongs securely to the currently authenticated identity
        $appointment = Appointment::where('client_id', Auth::guard('clients')->id())->findOrFail($id);

        // Relinquish ownership of the previous slot resource, making it available again
        $oldAvailability = Availability::find($appointment->availability_id);

        if ($oldAvailability) {
            $oldAvailability->update(['is_available' => true]);
        }

        // Secure the newly targeted slot constraint
        $newAvailability = Availability::where('date', $request->input('date'))
            ->where('hour', $request->input('time') . ':00')
            ->where('is_available', true)
            ->firstOrFail();

        // Update appointment references and downgrade status flag back to pending for administrative review
        $appointment->update([
            'availability_id' => $newAvailability->id,
            'status'          => 'pending'
        ]);

        // Synchronize pivot keys, dropping detached elements cleanly
        $serviceIds = collect($request->input('services'))->pluck('id');
        $appointment->services()->sync($serviceIds);

        // Lock down the newly requested block allocation
        $newAvailability->update(['is_available' => false]);

        return redirect()->route('agendar')->with('success', 'Agendamento atualizado com sucesso!');
    }
}
