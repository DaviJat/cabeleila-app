<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     *
     * @return Response
     */
    public function index(): Response
    {
        $clients = Client::orderBy('id', 'desc')->get();

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients
        ]);
    }

    /**
     * Store or update a client.
     *
     * @param ClientRequest $request
     * @return RedirectResponse
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $id = $request->input('id');

        // Unset the ID to prevent it from being passed as a column value 
        // to updateOrCreate, allowing it to act solely as the identifier.
        unset($validated['id']);

        Client::updateOrCreate(
            ['id' => $id],
            $validated
        );

        $message = $id ? 'Cliente atualizado com sucesso!' : 'Cliente cadastrado com sucesso!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified client.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->back()->with('success', 'Cliente excluído com sucesso!');
    }
}
