<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardAnalyticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardAnalyticsService $analyticsService
    ) {}

    /**
     * Display the admin dashboard with real-time statistics and charts.
     */
    public function index(): View
    {
        $metrics = $this->analyticsService->getDashboardMetrics();

        return view('admin.dashboard', $metrics);
    }
}
