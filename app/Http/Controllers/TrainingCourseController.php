<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trainingCourse($id)
    {
        $trainingCourse = TrainingCourse::with(['instructor','currency','instructorPer','category','vendor','course'])->where('instructor_id',$id)->get();
        return response()->json($trainingCourse);
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
            'course_id' => 'required|exists:courses,id',
            'instructor_per_id' => 'required|exists:instructor_pers,id',
            'currency_id' => 'required|exists:currencies,id',
            'rate' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $trainingCourses = new TrainingCourse($request->all());
        $trainingCourses->save();

        return response()->json($trainingCourses);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trainingCourses = TrainingCourse::where('instructor_id',$id)->get();

        return response()->json($trainingCourses);
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
            'course_id' => 'required|exists:courses,id',
            'instructor_per_id' => 'required|exists:instructor_pers,id',
            'currency_id' => 'required|exists:currencies,id',
            'rate' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $trainingCourses = TrainingCourse::findOrFail($id);

        $trainingCourses->update($request->all());

        return response()->json($trainingCourses);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trainingCourses = TrainingCourse::findOrFail($id);
        $trainingCourses->delete();
        return response()->json('deleted success');
    }
}
