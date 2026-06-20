<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Inertia\Inertia;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('company')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices
        ]);
    }
}
