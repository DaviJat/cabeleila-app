<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
