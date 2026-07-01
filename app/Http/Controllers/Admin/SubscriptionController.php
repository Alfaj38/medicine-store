<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['company', 'package'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions
        ]);
    }
}
