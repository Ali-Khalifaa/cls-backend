<?php

namespace App\Http\Controllers;

use App\Models\AssetsType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetsTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assets =AssetsType::all();

        return response()->json($assets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:assets_types,name',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $assets = AssetsType::create([
            'name' => $request->name,
        ]);
        return response()->json($assets);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $assets =AssetsType::findOrFail($id);
        return response()->json($assets);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:assets_types,name' . ($id ? ",$id" : ''),
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $assets =AssetsType::findOrFail($id);
        $assets->update([
            'name' => $request->name,
        ]);

        return response()->json($assets);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assets =AssetsType::find($id);
        $assets->delete();

        return response()->json('deleted success');

    }

}
