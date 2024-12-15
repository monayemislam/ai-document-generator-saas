<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'total_clients' => $user->clients()->count(),
            'total_projects' => Project::whereHas('client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'total_proposals' => Proposal::whereHas('project.client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'total_revenue' => Invoice::whereHas('project.client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'paid')->sum('amount'),
            'pending_amount' => Invoice::whereHas('project.client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'unpaid')->sum('amount'),
        ];

        $recentClients = $user->clients()
            ->latest()
            ->take(5)
            ->get();

        $recentProjects = Project::with('client')
            ->whereHas('client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        // Monthly Revenue Data
        $monthlyRevenue = Invoice::whereHas('project.client', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('status', 'paid')
        ->selectRaw('strftime("%Y-%m", created_at) as month, sum(amount) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->take(6)
        ->get();

        // Project Status Distribution
        $projectStatuses = Project::whereHas('client', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->selectRaw('
            CASE 
                WHEN end_date < CURRENT_DATE THEN "Completed"
                WHEN start_date <= CURRENT_DATE AND end_date >= CURRENT_DATE THEN "In Progress"
                ELSE "Upcoming"
            END as status,
            COUNT(*) as count
        ')
        ->groupBy('status')
        ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentClients' => $recentClients,
            'recentProjects' => $recentProjects,
            'chartData' => [
                'revenue' => [
                    'labels' => $monthlyRevenue->pluck('month'),
                    'datasets' => [
                        [
                            'label' => 'Monthly Revenue',
                            'data' => $monthlyRevenue->pluck('total'),
                            'borderColor' => '#10B981',
                            'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                            'fill' => true,
                        ]
                    ]
                ],
                'projectStatus' => [
                    'labels' => $projectStatuses->pluck('status'),
                    'datasets' => [
                        [
                            'data' => $projectStatuses->pluck('count'),
                            'backgroundColor' => ['#10B981', '#3B82F6', '#F59E0B'],
                        ]
                    ]
                ]
            ]
        ]);
    }
}
