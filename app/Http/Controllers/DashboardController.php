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

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentClients' => $recentClients,
            'recentProjects' => $recentProjects,
        ]);
    }
}
