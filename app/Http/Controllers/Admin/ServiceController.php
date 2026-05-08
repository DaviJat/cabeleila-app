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

        Service::create($validated);

        return redirect()->back()->with('success', 'Serviço cadastrado com sucesso!');
    }
}
