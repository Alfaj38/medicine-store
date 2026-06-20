<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::latest()->paginate(15);

        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons
        ]);
    }
}
