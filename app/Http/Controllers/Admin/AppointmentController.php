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
    // Store a new appointment, attach services, and mark the availability slot as occupied
    public function store(AppointmentRequest $request): RedirectResponse
    {
        try {
            // Use a database transaction to ensure data integrity
            DB::transaction(function () use ($request) {

                // 1. Create the new appointment (without the service_id column)
                $appointment = Appointment::create([
                    'availability_id' => $request->availability_id,
                    'client_id'       => $request->client_id,
                    'notes'           => $request->notes,
                    'status'          => 'confirmed',
                ]);

                // 2. Attach the multiple services to the pivot table
                $appointment->services()->attach($request->service_ids);

                // 3. Mark the availability slot as occupied
                $availability = Availability::find($request->availability_id);
                $availability->update([
                    'is_available' => false
                ]);
            });

            return redirect()->back()->with('success', 'Agendamento realizado com sucesso!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro ao processar o agendamento. Tente novamente.');
        }
    }
}
