<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SubscriptionPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = DB::table('subscription_payments')
            ->join('companies', 'subscription_payments.company_id', '=', 'companies.id')
            ->select('subscription_payments.*', 'companies.name as company_name')
            ->orderBy('subscription_payments.created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments
        ]);
    }
}
