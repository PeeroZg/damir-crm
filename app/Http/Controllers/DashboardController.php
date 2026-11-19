<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetrics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $mine = $request->query('scope') === 'mine';
        $metrics = DashboardMetrics::for($mine ? $request->user() : null);

        return view('dashboard', [
            'mine' => $mine,
            'kpis' => $metrics->kpis(),
            'pipeline' => $metrics->pipelineByStage(),
            'wonByMonth' => $metrics->wonByMonth(6),
            'tasks' => $metrics->upcomingTasks($request->user()),
            'closingSoon' => $metrics->closingSoon(),
            'recent' => $metrics->recentActivity(),
        ]);
    }
}
