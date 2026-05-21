<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Carbon\Carbon;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // Default fallback period: current week (Monday to Sunday)
        $defaultStart = Carbon::now()->startOfWeek()->toDateString(); // Monday
        $defaultEnd = Carbon::now()->endOfWeek()->toDateString();     // Sunday

        // Check if parameters exist in the request URL, otherwise apply default week
        $startDate = $request->has('start_date') ? $request->input('start_date') : $defaultStart;
        $endDate = $request->has('end_date') ? $request->input('end_date') : $defaultEnd;

        // Fetch appointments scoped by the date parameters
        $appointments = $this->getAppointmentsInPeriod($startDate, $endDate);

        // Process key performance indicators for the dashboard summary cards
        $summaryData = $this->getSummaryMetrics($appointments);

        // Process revenue evolution timeline metrics (Line Chart)
        $revenueTimeline = $this->getRevenueTimeline($appointments);

        // Process top booked services ranking metrics (Bar Chart)
        $topServices = $this->getTopServices($appointments);

        return Inertia::render('Admin/Dashboard/Index', [
            'summaryData'     => $summaryData,
            'revenueTimeline' => $revenueTimeline,
            'topServices'     => $topServices,
        ]);
    }

    /**
     * Query appointments within the chosen period via the 'availability' relation.
     */
    private function getAppointmentsInPeriod(?string $startDate, ?string $endDate): Collection
    {
        // If both dates are null, bypass the date scope to fetch all records
        if (is_null($startDate) && is_null($endDate)) {
            return Appointment::with(['services', 'availability'])
                ->withSum('services as total_services_price', 'price') // Sum up service prices
                ->get();
        }

        return Appointment::whereHas('availability', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })
            ->with(['services', 'availability']) // Load relations to safely map graphs without new database hits
            ->withSum('services as total_services_price', 'price') // Sum up service prices
            ->get();
    }

    /**
     * Extract total counters and calculate revenue based on status.
     */
    private function getSummaryMetrics(Collection $appointments): array
    {
        // Extract total counters based on status
        $totalAppointments = $appointments->count();
        $completed = $appointments->where('status', 'completed')->count();
        $confirmed = $appointments->where('status', 'confirmed')->count();
        $pending = $appointments->where('status', 'pending')->count();

        // Calculate revenue only from 'completed' appointments
        $totalRevenue = $appointments->where('status', 'completed')->sum('total_services_price');

        return [
            'revenue'      => (float) $totalRevenue,
            'appointments' => $totalAppointments,
            'completed'    => $completed,
            'confirmed'    => $confirmed,
            'pending'      => $pending,
        ];
    }

    /**
     * Group completed appointments revenue by formatted date strings.
     */
    private function getRevenueTimeline(Collection $appointments): array
    {
        $timeline = $appointments->where('status', 'completed')
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->availability->date)->format('d/m');
            })
            ->map(function ($dayGroup) {
                return $dayGroup->sum('total_services_price');
            })
            ->sortKeys();

        return [
            'labels' => $timeline->keys()->toArray(),
            'values' => $timeline->values()->toArray(),
        ];
    }

    /**
     * Flatten service relations from scope, group by name, and sort by descending count.
     */
    private function getTopServices(Collection $appointments): array
    {
        $services = $appointments->flatMap(function ($appointment) {
            return $appointment->services;
        })
            ->groupBy('name')
            ->map(function ($serviceGroup) {
                return $serviceGroup->count();
            })
            ->sortByDesc(fn($count) => $count)
            ->take(5);

        return [
            'labels' => $services->keys()->toArray(),
            'values' => $services->values()->toArray(),
        ];
    }
}
