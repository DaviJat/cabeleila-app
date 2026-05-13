<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index()
    {
        $availabilities = Availability::orderBy('date')
            ->orderBy('hour', 'asc')
            ->get();

        return Inertia::render('Admin/Schedule/Index', [
            'availabilities' => $availabilities
        ]);
    }
}
