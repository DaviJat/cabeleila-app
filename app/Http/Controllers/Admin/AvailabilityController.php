<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AvailabilityRequest;
use App\Models\Availability;
use Illuminate\Http\RedirectResponse;

class AvailabilityController extends Controller
{
    /**
     * Store or update an availability slot.
     *
     * @param AvailabilityRequest $request
     * @return RedirectResponse
     */
    public function store(AvailabilityRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $id = $request->input('id');

        // Unset the ID to prevent it from being passed as a column value 
        // to updateOrCreate, allowing it to act solely as the identifier.
        unset($validated['id']);

        Availability::updateOrCreate(
            ['id' => $id],
            $validated
        );

        $message = $id ? 'Horário atualizado com sucesso!' : 'Horário cadastrado com sucesso!';

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
