<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard metrics and charts based on scheduled dates.
     */
    public function index(Request $request): Response
    {
        // Define default dates if no filter is applied (current month)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Base query: Joining availabilities to filter by the ACTUAL SCHEDULED DATE, not created_at
        $baseQuery = Appointment::query()
            ->join('availabilities', 'appointments.availability_id', '=', 'availabilities.id')
            ->whereBetween('availabilities.date', [$startDate, $endDate]);

        // 1. Calculate General KPIs (Explicitly using appointments.status to avoid ambiguity)
        $totalAppointments = (clone $baseQuery)->whereIn('appointments.status', ['pending', 'confirmed', 'completed'])->count();
        $pending = (clone $baseQuery)->where('appointments.status', 'pending')->count();
        $confirmed = (clone $baseQuery)->where('appointments.status', 'confirmed')->count();
        $completed = (clone $baseQuery)->where('appointments.status', 'completed')->count();

        // Calculate Total Revenue joining services and availabilities
        $totalRevenue = DB::table('appointments')
            ->join('availabilities', 'appointments.availability_id', '=', 'availabilities.id')
            ->join('appointment_service', 'appointments.id', '=', 'appointment_service.appointment_id')
            ->join('services', 'appointment_service.service_id', '=', 'services.id')
            ->whereIn('appointments.status', ['confirmed', 'completed'])
            ->whereBetween('availabilities.date', [$startDate, $endDate])
            ->sum('services.price');

        // 2. Calculate Chart Data: Top 5 Services performed
        $topServices = DB::table('appointment_service')
            ->join('appointments', 'appointment_service.appointment_id', '=', 'appointments.id')
            ->join('availabilities', 'appointments.availability_id', '=', 'availabilities.id')
            ->join('services', 'appointment_service.service_id', '=', 'services.id')
            ->whereIn('appointments.status', ['pending', 'confirmed', 'completed']) // <-- Adicionado o 'pending' aqui!
            ->whereBetween('availabilities.date', [$startDate, $endDate])
            ->select('services.name', DB::raw('count(*) as total'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 3. Calculate Chart Data: Revenue over time (Grouped by Scheduled Date)
        $revenueOverTime = DB::table('appointments')
            ->join('availabilities', 'appointments.availability_id', '=', 'availabilities.id')
            ->join('appointment_service', 'appointments.id', '=', 'appointment_service.appointment_id')
            ->join('services', 'appointment_service.service_id', '=', 'services.id')
            ->whereIn('appointments.status', ['confirmed', 'completed'])
            ->whereBetween('availabilities.date', [$startDate, $endDate])
            ->select('availabilities.date', DB::raw('SUM(services.price) as daily_revenue'))
            ->groupBy('availabilities.date')
            ->orderBy('availabilities.date')
            ->get();

        // Return Inertia Response with formatted payload
        return Inertia::render('Admin/Dashboard', [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'kpis' => [
                'totalRevenue' => (float) $totalRevenue,
                'totalAppointments' => $totalAppointments,
                'completed' => $completed,
                'confirmed' => $confirmed,
                'pending' => $pending,
            ],
            'charts' => [
                'revenue' => [
                    // Format dates to DD/MM for better chart display using the new availabilities.date
                    'labels' => $revenueOverTime->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray(),
                    'data' => $revenueOverTime->pluck('daily_revenue')->toArray(),
                ],
                'services' => [
                    'labels' => $topServices->pluck('name')->toArray(),
                    'data' => $topServices->pluck('total')->toArray(),
                ],
            ],
        ]);
    }
}
