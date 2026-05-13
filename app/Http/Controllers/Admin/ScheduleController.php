<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Client;
use App\Models\Service;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index()
    {
        $availabilities = Availability::with('appointments')
            ->orderBy('date')
            ->orderBy('hour', 'asc')
            ->get();

        return Inertia::render('Admin/Schedule/Index', [
            'availabilities' => $availabilities,
            'clients' => Client::orderBy('full_name')->get(),
            'services' => Service::orderBy('name')->get(),
        ]);
    }
}
