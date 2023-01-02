<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $availability =Availability::all();

        return response()->json($availability);
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
            'title' => 'required|string|max:100|unique:availabilities,title',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $availability =Availability::create([
            'title' => $request->title,
        ]);
        return response()->json($availability);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $availability =Availability::findOrFail($id);
        return response()->json($availability);
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
            'title' => 'required|string|max:100|unique:availabilities,title' . ($id ? ",$id" : ''),
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $availability =Availability::findOrFail($id);
        $availability->update([
            'title' => $request->title,
        ]);

        return response()->json($availability);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $availability =Availability::findOrFail($id);
        $availability->delete();

        return response()->json('deleted success');
    }

}
