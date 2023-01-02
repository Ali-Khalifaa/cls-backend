<?php

namespace App\Http\Controllers;

use App\Models\Military;
use Illuminate\Http\Request;

class MilitaryController extends Controller
{
    public function index(){
        $militaries = Military::all();
        return response()->json($militaries);
    }
}
