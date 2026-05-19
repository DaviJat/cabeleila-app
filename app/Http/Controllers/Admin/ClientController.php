<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\Client;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        // Listando clientes, possivelmente com paginação para melhor performance
        $clients = Client::orderBy('id', 'desc')->paginate(15);

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients
        ]);
    }

    public function store(ClientRequest $request)
    {
        $validated = $request->validated();
        $id = $request->input('id');

        // Remove o ID para não tentar atualizar a chave primária diretamente
        unset($validated['id']);

        Client::updateOrCreate(
            ['id' => $id],
            $validated
        );

        $mensagem = $id ? 'Cliente atualizado com sucesso!' : 'Cliente cadastrado com sucesso!';

        return redirect()->back()->with('success', $mensagem);
    }

    public function destroy(int $id)
    {
        $client = Client::findOrFail($id);

        $client->delete();

        return redirect()->back()->with('success', 'Cliente excluído com sucesso!');
    }
}
