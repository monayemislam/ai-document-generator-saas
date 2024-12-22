<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Add debugging
        \Log::info('User ID: ' . $user->id);
        
        $stats = [
            'total_clients' => $user->clients()->count(),
            'total_documents' => Document::where('user_id', $user->id)->count(),
            'total_proposals' => Proposal::whereHas('project.client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'total_revenue' => Invoice::whereHas('project.client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'paid')->sum('amount') ?? 0,
            'pending_amount' => Invoice::whereHas('project.client', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'unpaid')->sum('amount') ?? 0,
        ];

        // Add debugging for each stat
        \Log::info('Dashboard Stats:', $stats);

        // Debug queries
        \DB::enableQueryLog();
        
        $recentDocuments = Document::where('user_id', $user->id)
            ->select([
                'id',
                'project_title',
                'client_name',
                'created_at',
                'start_date',
                'end_date',
                'project_type'
            ])
            ->latest()
            ->take(5)
            ->get();

        $documentStatuses = Document::where('user_id', $user->id)
            ->selectRaw("
                CASE 
                    WHEN date(end_date) < date('now') THEN 'Completed'
                    WHEN date(start_date) <= date('now') AND date(end_date) >= date('now') THEN 'In Progress'
                    ELSE 'Upcoming'
                END as status,
                COUNT(*) as count
            ")
            ->groupBy('status')
            ->get();

        \Log::info('SQL Queries:', \DB::getQueryLog());

        // Monthly Revenue Data
        $monthlyRevenue = Invoice::whereHas('project.client', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('status', 'paid')
        ->selectRaw("strftime('%Y-%m', created_at) as month, sum(amount) as total")
        ->groupBy('month')
        ->orderBy('month')
        ->take(6)
        ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentDocuments' => $recentDocuments,
            'recentClients' => $user->clients()->latest()->take(5)->get(),
            'chartData' => [
                'revenue' => [
                    'labels' => $monthlyRevenue->pluck('month'),
                    'datasets' => [[
                        'label' => 'Monthly Revenue',
                        'data' => $monthlyRevenue->pluck('total'),
                        'backgroundColor' => '#10B981',
                        'borderRadius' => 6,
                    ]]
                ],
                'documentStatus' => [
                    'labels' => $documentStatuses->pluck('status'),
                    'datasets' => [
                        [
                            'data' => $documentStatuses->pluck('count'),
                            'backgroundColor' => [
                                '#10B981', // Completed - Green
                                '#3B82F6', // In Progress - Blue
                                '#F59E0B'  // Upcoming - Orange
                            ],
                            'hoverOffset' => 4
                        ]
                    ]
                ]
            ]
        ]);
    }
}
