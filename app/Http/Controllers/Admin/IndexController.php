<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class IndexController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
        $this->middleware('permission:dashboard.view')
            ->only('index');
    }

    public function index()
    {
        return view(
            'Admin.dashboard',
            $this->dashboardService->getSummary()
        );
    }
}
 