<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // For demonstration purposes, we will return some mock data for the charts.
        // In a real application, these would be queries over your payments and companies tables.

        $revenueData = [
            'series' => [
                [
                    'name' => 'MRR',
                    'data' => [12000, 15000, 14500, 18000, 22000, 25000, 29000]
                ]
            ],
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']
        ];

        $growthData = [
            'series' => [
                [
                    'name' => 'Active Tenants',
                    'data' => [12, 15, 18, 22, 30, 45, 60]
                ]
            ],
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']
        ];

        return Inertia::render('Admin/Analytics/Index', [
            'revenueData' => $revenueData,
            'growthData' => $growthData,
        ]);
    }
}
