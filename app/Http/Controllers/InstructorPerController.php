<?php

namespace App\Http\Controllers;

use App\Models\InstructorPer;
use Illuminate\Http\Request;

class InstructorPerController extends Controller
{
    public function index(){
        return response()->json(InstructorPer::all());
    }
}
