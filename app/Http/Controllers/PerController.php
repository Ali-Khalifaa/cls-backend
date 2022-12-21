<?php

namespace App\Http\Controllers;

use App\Models\Per;
use Illuminate\Http\Request;

class PerController extends Controller
{
    public function index()
    {
        $per = Per::all();

        return response()->json($per);
    }
}
