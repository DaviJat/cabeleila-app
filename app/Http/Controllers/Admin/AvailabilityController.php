<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AvailabilityRequest;
use App\Models\Availability;
use Inertia\Inertia;

class AvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = Availability::orderBy('id')->get();

        return Inertia::render('Admin/Availabilities/Index', [
            'availabilities' => $availabilities
        ]);
    }

    // Cria ou atualiza um horário de disponibilidade
    public function store(AvailabilityRequest $request)
    {
        $validated = $request->validated();
        $id = $request->input('id');

        // Remove o ID do array de validação para evitar problemas com o updateOrCreate
        unset($validated['id']);

        // Se o ID for fornecido, atualiza o horário existente; caso contrário, cria um novo horário
        Availability::updateOrCreate(
            ['id' => $id],
            $validated
        );

        $mensagem = $id ? 'Horário atualizado com sucesso!' : 'Horário cadastrado com sucesso!';

        return redirect()->back()->with('success', $mensagem);
    }

    // Exclui um horário de disponibilidade
    public function destroy(int $id)
    {
        $availability = Availability::findOrFail($id);

        // Horários indisponíveis não podem ser excluídos, pois estão associados a agendamentos
        if (!$availability->is_available) {
            return redirect()->back()->with('error', 'Este horário não pode ser excluído pois já está associado a um agendamento.');
        }

        $availability->delete();

        return redirect()->back()->with('success', 'Horário removido com sucesso!');
    }
}
