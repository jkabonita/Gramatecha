<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index(): Response
    {
        $user = auth()->user();

        // Get basic statistics
        $stats = [
            'total_presentations' => Presentation::count(),
            'total_users' => User::count(),
            'user_presentations' => $user ? Presentation::where('uploaded_by', $user->id)->count() : 0,
            'total_views' => Presentation::sum('views_count') ?? 0,
        ];

        // Get recent presentations
        $recentPresentations = Presentation::with('uploader:id,name,email')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($presentation) => [
                'id' => $presentation->id,
                'title' => $presentation->title,
                'uploaded_by' => $presentation->uploader->name ?? 'Unknown',
                'created_at' => $presentation->created_at->diffForHumans(),
                'views_count' => $presentation->views_count,
            ]);

        // Get presentations by month (last 6 months)
        $presentationsByMonth = Presentation::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month,
                'count' => $item->count,
            ]);

        // Get top uploaders
        $topUploaders = User::withCount('presentations')
            ->having('presentations_count', '>', 0)
            ->orderBy('presentations_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'presentations_count' => $user->presentations_count,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentPresentations' => $recentPresentations,
            'presentationsByMonth' => $presentationsByMonth,
            'topUploaders' => $topUploaders,
        ]);
    }
}
