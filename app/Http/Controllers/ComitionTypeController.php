<?php

namespace App\Http\Controllers;

use App\Models\CommissionType;
use Illuminate\Http\Request;

class ComitionTypeController extends Controller
{
    public function index()
    {
        $commissions = CommissionType::all();

        return response()->json($commissions);
    }
}
