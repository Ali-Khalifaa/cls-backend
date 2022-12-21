<?php

namespace App\Http\Controllers;

use App\Models\TrainingDiploma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingDiplomaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trainingDiploma($id)
    {
        $trainingDiploma = TrainingDiploma::with(['instructor','currency','instructorPer','category','vendor','diploma'])->where('instructor_id',$id)->get();
        return response()->json($trainingDiploma);
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
            'instructor_id' => 'required|exists:instructors,id',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'diploma_id' => 'required|exists:diplomas,id',
            'instructor_per_id' => 'required|exists:instructor_pers,id',
            'currency_id' => 'required|exists:currencies,id',
            'rate' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $trainingDiplomas = new TrainingDiploma($request->all());
        $trainingDiplomas->save();

        return response()->json($trainingDiplomas);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trainingDiplomas = TrainingDiploma::where('instructor_id',$id)->get();

        return response()->json($trainingDiplomas);
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
            'instructor_id' => 'required|exists:instructors,id',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'diploma_id' => 'required|exists:diplomas,id',
            'instructor_per_id' => 'required|exists:instructor_pers,id',
            'currency_id' => 'required|exists:currencies,id',
            'rate' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $trainingDiploma = TrainingDiploma::findOrFail($id);
        $trainingDiploma->update($request->all());

        return response()->json($trainingDiploma);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trainingDiploma = TrainingDiploma::findOrFail($id);
        $trainingDiploma->delete();
        return response()->json('deleted success');
    }
}
