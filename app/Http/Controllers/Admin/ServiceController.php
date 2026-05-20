<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     *
     * @return Response
     */
    public function index(): Response
    {
        $services = Service::orderBy('id', 'desc')->get();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services
        ]);
    }

    /**
     * Store or update a service.
     *
     * @param ServiceRequest $request
     * @return RedirectResponse
     */
    public function store(ServiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $id = $request->input('id');

        // We unset the ID to prevent it from being passed as a column value 
        // to updateOrCreate, allowing it to act solely as the identifier.
        unset($validated['id']);

        Service::updateOrCreate(
            ['id' => $id],
            $validated
        );

        $message = $id ? 'Serviço atualizado com sucesso!' : 'Serviço cadastrado com sucesso!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified service.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->back()->with('success', 'Serviço excluído com sucesso!');
    }
}
