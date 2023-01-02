<?php

namespace App\Http\Controllers;

use App\Imports\CourseImport;
use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CourseController extends Controller
{

    /**
     * import data excel
     */
    public function courseImport(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        Excel::import(new CourseImport, $request->file('file')->store('temp'));

        return response()->json("successfully");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::with(['coursePrices','caterings'])->get();

        return response()->json($courses);

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
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'hour_count' => 'required|numeric',
            'course_code' => 'nullable|string|max:100|unique:courses,course_code',
            'configuration_pcs' => 'nullable|string|max:300',
            'catering'=>'required|array|min:1',
            'catering.*'=>'exists:caterings,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $course = Course::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'vendor_id' => $request->vendor_id,
            'hour_count' => $request->hour_count,
            'course_code' => $request->course_code,
            'configuration_pcs' => $request->configuration_pcs,
        ]);

        $course->caterings()->attach($request->catering);

        return response()->json($course);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::with(['coursePrices','caterings'])->findOrFail($id);
        return response()->json($course);
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
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'hour_count' => 'required|numeric',
            'configuration_pcs' => 'nullable|string|max:300',
            'course_code' => 'required|string|max:100|unique:courses,course_code' . ($id ? ",$id" : ''),
            'catering'=>'required|array|min:1',
            'catering.*'=>'exists:caterings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $course = Course::findOrFail($id);
        $course->caterings()->sync($request->catering);
        $course->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'vendor_id' => $request->vendor_id,
            'hour_count' => $request->hour_count,
            'course_code' => $request->course_code,
            'configuration_pcs' => $request->configuration_pcs,
        ]);

        return response()->json($course);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json('deleted success');
    }


    /**
     * Activation course.
     */

    public function activationCourse(int $id)
    {

        $course = Course::findOrFail($id);
        if ($course->active == 1){

            $course->update([
                'active' => 0,
            ]);

        }else{

            $course->update([
                'active' => 1,
            ]);
        }

        return response()->json($course);
    }

    /**
     * get Active courses.
     */
    public function getActiveCourse()
    {
        $courses = Course::where('active',1)->get();
        return response()->json($courses);
    }

    /**
     * get des Active courses.
     */
    public function getDeactivateCourse()
    {
        $courses = Course::where('active',0)->get();
        return response()->json($courses);
    }


}
