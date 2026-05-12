<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('id', 'desc')->get();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services
        ]);
    }

    // Cria ou atualiza um serviço
    public function store(ServiceRequest $request)
    {
        // Valida os dados usando as regras definidas no SaveServiceRequest
        $validated = $request->validated();

        $id = $request->input('id');

        // Remove o ID do array de validação para evitar problemas com o updateOrCreate
        unset($validated['id']);

        // Se o ID for fornecido, atualiza o serviço existente; caso contrário, cria um novo serviço
        Service::updateOrCreate(
            ['id' => $id],
            $validated
        );

        $mensagem = $id ? 'Serviço atualizado com sucesso!' : 'Serviço cadastrado com sucesso!';

        return redirect()->back()->with('success', $mensagem);
    }

    public function destroy(int $id)
    {
        $service = Service::findOrFail($id);

        // Preenche a coluna deleted_at (Soft Delete)
        $service->delete();

        return redirect()->back()->with('success', 'Serviço excluído com sucesso!');
    }
}
