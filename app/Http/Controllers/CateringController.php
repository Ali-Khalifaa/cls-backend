<?php

namespace App\Http\Controllers;

use App\Models\Catering;
use Illuminate\Http\Request;

class CateringController extends Controller
{
    public function index(){
        $catering = Catering::all();
        return response()->json($catering);
    }
}
