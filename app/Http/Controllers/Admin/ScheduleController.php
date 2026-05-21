<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Client;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    /**
     * Display the schedule and availability overview.
     *
     * @return Response
     */
    public function index(): Response
    {
        // Eager load appointments along with their nested relations (services and client)
        // to prevent N+1 query performance issues when rendering the calendar/schedule.
        $availabilities = Availability::with(['appointments.services', 'appointments.client'])
            ->orderBy('date')
            ->orderBy('hour', 'asc')
            ->get();

        return Inertia::render('Admin/Schedule/Index', [
            'availabilities' => $availabilities,
            'clients'        => Client::orderBy('full_name')->get(),
            'services'       => Service::orderBy('name')->get(),
        ]);
    }
}
