<?php

namespace App\Http\Controllers;

use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function divisions()
    {
        return response()->json(Division::select('id', 'name')->orderBy('name')->get());
    }

    public function districts($division_id)
    {
        return response()->json(District::where('division_id', $division_id)->select('id', 'name')->orderBy('name')->get());
    }

    public function upazilas($district_id)
    {
        return response()->json(Upazila::where('district_id', $district_id)->select('id', 'name')->orderBy('name')->get());
    }
}
