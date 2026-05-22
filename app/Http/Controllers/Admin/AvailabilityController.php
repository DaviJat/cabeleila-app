<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AvailabilityRequest;
use App\Models\Availability;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class AvailabilityController extends Controller
{
    /**
     * Store or update an availability slot.
     * Generates a WhatsApp rescheduling notification if an occupied slot's time/date shifts.
     *
     * @param AvailabilityRequest $request
     * @param WhatsAppService $whatsAppService
     * @return RedirectResponse
     */
    public function store(AvailabilityRequest $request, WhatsAppService $whatsAppService): RedirectResponse
    {
        $validated = $request->validated();
        $id = $request->input('id');
        unset($validated['id']);

        $whatsAppRedirectUrl = null;

        DB::transaction(function () use ($id, $validated, $whatsAppService, &$whatsAppRedirectUrl) {
            if ($id) {
                // Fetch existing slot and eager load any active appointments inside it
                $availability = Availability::with(['appointments' => function ($query) {
                    $query->where('status', '!=', 'canceled')->with(['client', 'services']);
                }])->findOrFail($id);

                $inputDate = $validated['date'];
                $inputHour = $validated['hour'];

                // Postgres standardizes TIME columns to HH:MM:SS, ensure strict matching
                $dbInputHour = strlen($inputHour) === 5 ? $inputHour . ':00' : $inputHour;

                // Verify if the core temporal matrix actually shifted
                $timeChanged = ($availability->date !== $inputDate) || ($availability->hour !== $dbInputHour);

                $availability->update($validated);

                // If time shifted and there's an active client attached, generate rescheduling payload
                if ($timeChanged && $availability->appointments->isNotEmpty()) {
                    $activeAppointment = $availability->appointments->first();

                    $whatsAppRedirectUrl = $whatsAppService->getAdminActionNotificationUrl(
                        $activeAppointment->client,
                        $activeAppointment,
                        false // Passing false triggers the "remarcado" text mutation
                    );
                }
            } else {
                // Standard creation for new empty slots
                $validated['is_available'] = true;
                Availability::create($validated);
            }
        });

        $message = $id ? 'Horário atualizado com sucesso!' : 'Horário cadastrado com sucesso!';

        // Dispatch URL back to Inertia flash space if a reschedule occurred
        if ($whatsAppRedirectUrl) {
            return redirect()->back()->with('whatsapp_url', $whatsAppRedirectUrl)->with('success', $message);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified availability slot.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $availability = Availability::findOrFail($id);

        // Unavailable slots cannot be deleted as they are either linked to an appointment 
        // or have already expired.
        if (! $availability->is_available) {
            return redirect()->back()->with('error', 'Este horário não pode ser excluído pois já está associado a um agendamento ou já expirou.');
        }

        $availability->delete();

        return redirect()->back()->with('success', 'Horário removido com sucesso!');
    }
}
