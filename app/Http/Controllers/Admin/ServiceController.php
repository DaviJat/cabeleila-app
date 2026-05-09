<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'               => ['nullable', 'integer', 'exists:services,id'], // Adicionamos o ID
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string', 'max:1000'],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'name.required'             => 'O nome do serviço é obrigatório.',
            'description.required'      => 'A descrição é obrigatória.',
            'price.required'            => 'O preço é obrigatório.',
            'price.numeric'             => 'O preço deve ser um valor numérico.',
            'duration_minutes.required' => 'A duração é obrigatória.',
            'duration_minutes.min'      => 'A duração mínima é de 1 minuto.',
        ]);

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
}
