<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CoursePrice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoursePricesController extends Controller
{
    /**
     * get course price
     */
    public function coursePrice($id)
    {
        $coursePrices = CoursePrice::with(['materials'=>function($q){
            $q->with('material');
        }])->findOrFail($id);
        return response()->json($coursePrices);
    }

    /**
     * get course price now
     */
    public function coursePriceNow($id)
    {
        $coursePrices = CoursePrice::with(['materials'=>function($q){
            $q->with('material');
        }])->where('course_id',$id)
            ->whereDate('from_date','<=',now())
            ->whereDate('active_date','>=',now())->first();

        if (!$coursePrices)
        {
            $coursePrices = CoursePrice::with(['materials'=>function($q){
                $q->with('material');
            }])->where('course_id',$id)->get()->last();
        }

        return response()->json($coursePrices);
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
            'course_id' => 'required|exists:courses,id',
            'before_discount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'after_discount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'corporate' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'private' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'online' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'protocol' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'corporate_group' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'official' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_cd' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_flash_memory' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'hard_copy' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'lab_virtual' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'membership_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'application_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'exam_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'block_note' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'pen' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'training_kit' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'from_date' => 'required',
            'active_date' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $date = date('Y-m-d',strtotime( $request->active_date ));
        $from_date = date('Y-m-d',strtotime( $request->from_date ));
        $request_data=$request->all();
        $request_data['active_date'] =$date;
        $request_data['from_date'] =$from_date;

        $coursePrices = new CoursePrice($request_data);
        $coursePrices->save();
//        foreach ($request['materials'] as $material){
//            CourseMaterial::create([
//                'course_price_id' => $coursePrices->id,
//                'material_id' => $material['material_id'],
//                'material_price' => $material['material_price'],
//            ]);
//        }

        return response()->json($coursePrices);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coursePrices = CoursePrice::with(['materials'=>function($q){
            $q->with('material');
        }])->where('course_id',$id)->get();

        return response()->json($coursePrices);

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
            'course_id' => 'required|exists:courses,id',
            'before_discount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'after_discount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'corporate' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'private' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'online' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'protocol' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'corporate_group' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'official' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_cd' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_flash_memory' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'hard_copy' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'lab_virtual' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'membership_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'application_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'exam_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'block_note' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'pen' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'training_kit' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'from_date' => 'required',
            'active_date' => 'required',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.material_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        $date = date('Y-m-d',strtotime( $request->active_date ));
        $from_date = date('Y-m-d',strtotime( $request->from_date ));
        $request_data=$request->all();
        $request_data['active_date'] =$date;
        $request_data['from_date'] =$from_date;

        $coursePrices = CoursePrice::findOrFail($id);
        $coursePrices->update($request_data);
//        foreach ($coursePrices->materials as $item){
//            $item->delete();
//        }
//
//        foreach ($request['materials'] as $material){
//            CourseMaterial::create([
//                'course_price_id' => $coursePrices->id,
//                'material_id' => $material['material_id'],
//                'material_price' => $material['material_price'],
//            ]);
//        }

        return response()->json($coursePrices);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coursePrices = CoursePrice::findOrFail($id);
        $coursePrices->delete();
        return response()->json('deleted success');
    }
}
