<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Models\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get all dashboard data
        $statistics = $this->getBasicStatistics();
        $monthlyAppointments = $this->getMonthlyAppointments();
        $monthlyRevenue = $this->getMonthlyRevenue();
        $servicePopularity = $this->getServicePopularity();
        $statusDistribution = $this->getStatusDistribution();

        // Convert data to JSON for JavaScript
        $chartData = [
            'statistics' => $statistics,
            'monthlyAppointments' => $monthlyAppointments,
            'monthlyRevenue' => $monthlyRevenue,
            'servicePopularity' => $servicePopularity,
            'statusDistribution' => $statusDistribution
        ];

        // dd($chartData);

        // Pass data to view
        return view('dashboard.pages.dashboard', compact('user', 'chartData'));
    }

    /**
     * Get basic statistics for the dashboard.
     */
    private function getBasicStatistics()
    {
        $totalAppointments = Appointment::count();
        $totalRevenue = Payment::sum('amount');
        $totalCustomers = User::count();

        // Get most popular service
        $popularService = DB::table('appointment_services')
            ->select('service_id', DB::raw('count(*) as count'))
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->first();

        $popularServiceName = '-';
        $popularServiceCount = 0;

        if ($popularService) {
            $service = Service::find($popularService->service_id);
            $popularServiceName = $service ? $service->service_name : '-';
            $popularServiceCount = $popularService->count;
        }

        return [
            'totalAppointments' => $totalAppointments,
            'totalRevenue' => $totalRevenue,
            'totalCustomers' => $totalCustomers,
            'popularService' => [
                'name' => $popularServiceName,
                'count' => $popularServiceCount
            ]
        ];
    }

    /**
     * Get monthly appointments data for the last 6 months.
     */
    private function getMonthlyAppointments()
    {
        $months = [];
        $appointmentCounts = [];

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;

            $count = Appointment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $appointmentCounts[] = $count;
        }

        return [
            'labels' => $months,
            'values' => $appointmentCounts
        ];
    }

    /**
     * Get monthly revenue data for the last 6 months.
     */
    private function getMonthlyRevenue()
    {
        $months = [];
        $revenueCounts = [];

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;

            $revenue = Payment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $revenueCounts[] = $revenue;
        }

        return [
            'labels' => $months,
            'values' => $revenueCounts
        ];
    }

    /**
     * Get service popularity data.
     */
    private function getServicePopularity()
    {
        $services = DB::table('appointment_services')
            ->join('services', 'appointment_services.service_id', '=', 'services.id')
            ->select('services.service_name', DB::raw('count(*) as count'))
            ->groupBy('services.service_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $labels = $services->pluck('service_name')->toArray();
        $values = $services->pluck('count')->toArray();

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Get appointment status distribution.
     */
    private function getStatusDistribution()
    {
        $statuses = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $statusMap = [
            'scheduled' => 'Scheduled',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        $labels = [];
        $values = [];

        foreach ($statuses as $status) {
            $labels[] = $statusMap[$status->status] ?? $status->status;
            $values[] = $status->count;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
}
