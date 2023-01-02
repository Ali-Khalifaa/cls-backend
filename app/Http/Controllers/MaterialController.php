<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(){
        $materials = Material::all();
        return response()->json($materials);
    }
}
