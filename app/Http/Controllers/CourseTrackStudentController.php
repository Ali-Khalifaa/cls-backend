<?php

namespace App\Http\Controllers;

use App\Models\CourseTrack;
use App\Models\CourseTrackStudent;
use App\Models\CourseTrackStudentComment;
use App\Models\CourseTrackStudentDiscount;
use App\Models\CourseTrackStudentPayment;
use App\Models\CourseTrackStudentPrice;
use App\Models\Lead;
use App\Models\LeadFile;
use App\Models\TargetEmployees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseTrackStudentController extends Controller
{
    /**
     * get register course track by employee id and course track id
     */
    public function registerCourseTrackByEmployeeIdAndCourseTrackId($employee_id, $course_track_id)
    {
        $course_track_students = CourseTrackStudent::with(['catering' => function ($q) {
            $q->with('catering');
        }, 'lead', 'courseTrack', 'employee', 'courseTrackStudentPrice', 'courseTrackStudentDiscount', 'courseTrackStudentPayment'])->where([
            ['course_track_id', $course_track_id],
            ['employee_id', $employee_id],
            ['cancel', 0],
        ])->get();

        foreach ($course_track_students as $course_track_student) {
            $total_paid = 0;

            foreach ($course_track_student->courseTrackStudentPayment as $payment) {
                if ($payment->checkIs_paid == 1) {
                    $total_paid += $payment->all_paid;
                }

            }
            $course_track_student->total_paid = $total_paid;
        }

        return response()->json($course_track_students);
    }

    /**
     * Transfer To another Salesman
     */
    public function TransferToAnotherSalesman(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_track_student_id' => 'required|exists:course_track_students,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }

        $course_track_student = CourseTrackStudent::findOrFail($request->course_track_student_id);
        $course_track_student['lead']->update([
            'employee_id' => $request->employee_id,
        ]);
        $course_track_student->update([
            'employee_id' => $request->employee_id,
        ]);

        return response()->json($course_track_student);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'course_track_id' => 'required|exists:course_tracks,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }

        $request_data = $request->all();

        if ($request->lead_id == "null") {
            $validator = Validator::make($request->all(), [
                'name_en' => 'required|string|max:150',
                'name_ar' => 'nullable|string|max:150',
                'registration_remark' => 'nullable|string|max:250',
                'mobile' => 'required|unique:leads,mobile',
                'phone' => 'nullable|unique:leads,phone',
                'email' => 'required|string|email|max:255|unique:leads,email',
                'country_id' => 'nullable|exists:countries,id',
                'state_id' => 'nullable|exists:states,id',
                'interesting_level_id' => 'required|exists:interesting_levels,id',
                'lead_source_id' => 'required|exists:lead_sources,id',
                'Job_title' => 'nullable|string|max:150',
                'company_name' => 'nullable|string|max:150',
                'birth_day' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json($errors, 422);
            }

            $lead = Lead::create([
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'registration_remark' => $request->registration_remark,
                'mobile' => $request->mobile,
                'phone' => $request->phone,
                'email' => $request->email,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'interesting_level_id' => $request->interesting_level_id,
                'lead_source_id' => $request->lead_source_id,
                'employee_id' => $request->employee_id,
                'Job_title' => $request->Job_title,
                'company_name' => $request->company_name,
                'birth_day' => $request->birth_day,
                'is_client' => 1
            ]);

            // file upload

            if ($request->hasFile('file')) {
                $img = $request->file('file');
                $ext = $img->getClientOriginalExtension();
                $image_name = $lead->id . "-" . $request->file_name . ".$ext";
                $img->move(public_path('uploads/leads/'), $image_name);

                LeadFile::create([
                    'name' => $request->file_name,
                    'type' => $ext,
                    'file' => $image_name,
                    'lead_id' => $lead->id,
                ]);
            }

            $request_data['lead_id'] = $lead->id;
        } else {

            $lead = Lead::findOrFail($request->lead_id);
            $lead->update([
                'is_client' => 1
            ]);
        }

        $course_track = CourseTrack::findOrFail($request_data['course_track_id']);

        $course_track_student = CourseTrackStudent::create([
            'lead_id' => $request_data['lead_id'],
            'course_track_id' => $request_data['course_track_id'],
            'employee_id' => $request_data['employee_id'],
            'course_id' => $course_track->course_id,
        ]);

//        $course_track_student->materials()->syncWithoutDetaching($request->materials);
        if ($request->catering !== null && $request->catering !== "null" && $request->catering !== "undefined"){
            $catering_ids = preg_split("/[,]/", $request->catering);
            $course_track_student->catering()->syncWithoutDetaching($catering_ids);
        }
        $request_data['course_track_student_id'] = $course_track_student->id;


        $course_track_student_price = CourseTrackStudentPrice::create([
            'course_track_student_id' => $request_data['course_track_student_id'],
            'final_price' => $request_data['final_price'] == "null" || $request_data['final_price'] == "undefined" ? 0 : $request_data['final_price'],
            'total_discount' => $request_data['total_discount'] == "null" || $request_data['total_discount'] == "undefined" ? 0 : $request_data['total_discount'],
            'course_price' => $request_data['price'] == "null" || $request_data['price'] == "undefined" ? 0 : $request_data['price'],
            'corporate' => $request_data['corporate'] == "null" || $request_data['corporate'] == "undefined" ? 0 : $request_data['corporate'],
            'private' => $request_data['private'] == "null" || $request_data['private'] == "undefined" ? 0 : $request_data['private'],
            'online' => $request_data['online'] == "null" || $request_data['online'] == "undefined" ? 0 : $request_data['online'],
            'protocol' => $request_data['protocol'] == "null" || $request_data['protocol'] == "undefined" ? 0 : $request_data['protocol'],
            'corporate_group' => $request_data['corporate_group'] == "null" || $request_data['corporate_group'] == "undefined" ? 0 : $request_data['corporate_group'],
            'official' => $request_data['official'] == "null" || $request_data['official'] == "undefined" ? 0 : $request_data['official'],
            'soft_copy_cd' => $request_data['soft_copy_cd'] == "null" || $request_data['soft_copy_cd'] == "undefined" ? 0 : $request_data['soft_copy_cd'],
            'soft_copy_flash_memory' => $request_data['soft_copy_flash_memory'] == "null" || $request_data['soft_copy_flash_memory'] == "undefined" ? 0 : $request_data['soft_copy_flash_memory'],
            'hard_copy' => $request_data['hard_copy'] == "null" || $request_data['hard_copy'] == "undefined" ? 0 : $request_data['hard_copy'],
            'lab_virtual' => $request_data['lab_virtual'] == "null" || $request_data['lab_virtual'] == "undefined" ? 0 : $request_data['lab_virtual'],
            'membership_price' => $request_data['membership_price'] == "null" || $request_data['membership_price'] == "undefined" ? 0 : $request_data['membership_price'],
            'application_price' => $request_data['application_price'] == "null" || $request_data['application_price'] == "undefined" ? 0 : $request_data['application_price'],
            'exam_price' => $request_data['exam_price'] == "null" || $request_data['exam_price'] == "undefined" ? 0 : $request_data['exam_price'],
            'block_note' => $request_data['block_note'] == "null" || $request_data['block_note'] == "undefined" ? 0 : $request_data['block_note'],
            'pen' => $request_data['pen'] == "null" || $request_data['pen'] == "undefined" ? 0 : $request_data['pen'],
            'training_kit' => $request_data['training_kit'] == "null" || $request_data['training_kit'] == "undefined" ? 0 : $request_data['training_kit'],
        ]);

        //replase boolean

        $tempData = str_replace("\\", "", $request->discounts);

        $request_data['discounts'] = json_decode($tempData);

        if (count($request_data['discounts']) > 0) {
            foreach ($request_data['discounts'] as $discount) {
                $course_track_student_discount = CourseTrackStudentDiscount::create([
                    'course_track_student_id' => $course_track_student->id,
                    'discount_id' => $discount->id,
                ]);

            }
        }

        CourseTrackStudentPayment::create([
            'course_track_student_id' => $request_data['course_track_student_id'],
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'comment' => $request->comment,
        ]);

        if ($request_data['2nd_date'] != 'null' && $request_data['2nd_amount'] != 'null') {
            CourseTrackStudentPayment::create([
                'course_track_student_id' => $request_data['course_track_student_id'],
                'payment_date' => $request_data['2nd_date'],
                'amount' => $request_data['2nd_amount'],
            ]);
        }

        if ($request_data['3rd_date'] != 'null' && $request_data['3rd_amount'] != 'null') {
            CourseTrackStudentPayment::create([
                'course_track_student_id' => $request_data['course_track_student_id'],
                'payment_date' => $request_data['3rd_date'],
                'amount' => $request_data['3rd_amount'],
            ]);
        }

        if ($request_data['4th_date'] != 'null' && $request_data['4th_amount'] != 'null') {
            CourseTrackStudentPayment::create([
                'course_track_student_id' => $request_data['course_track_student_id'],
                'payment_date' => $request_data['4th_date'],
                'amount' => $request_data['4th_amount'],
            ]);
        }

//        $targetEmployee = TargetEmployees::with(['salesTarget'=>function($q){
//            $q-> where('to_date','>',now());
//        }])->where([
//
//            ['employee_id','=',$request->employee_id],
//            ['target_amount','>','achievement'],
//
//        ])->first();
//        if ($targetEmployee != null){
//
//            if ($targetEmployee->salesTarget != null)
//            {
//                $achievement = $targetEmployee->achievement + $request_data['final_price'];
//                $targetEmployee->update([
//                    'achievement' => $achievement
//                ]);
//            }
//        }
        return response()->json($course_track_student);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course_track_student = CourseTrackStudent::
        with(['materials' => function ($q) {
            $q->with('material');
        }, 'catering' => function ($q) {
            $q->with('catering');
        }, 'lead', 'courseTrack', 'employee', 'courseTrackStudentPrice', 'courseTrackStudentDiscount', 'courseTrackStudentPayment'])->findOrFail($id);

        return response()->json($course_track_student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'course_track_id' => 'required|exists:course_tracks,id',
//            'final_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
//            'payment_additional_amount' => 'regex:/^\d+(\.\d{1,2})?$/',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }
        $course_track_student = CourseTrackStudent::findOrFail($id);

        $payments = $course_track_student->courseTrackStudentPayment;
        if (count($payments) > 0) {
            foreach ($payments as $payment) {
                if ($payment->checkIs_paid == 0) {
                    $payment->delete();
                }
            }
        }

        $discounts = $course_track_student->courseTrackStudentDiscount;

        if (count($discounts) > 0) {
            foreach ($discounts as $discount) {
                $discount->delete();
            }
        }

        $course_track_student->update([
            'course_track_id' => $request->course_track_id,
        ]);

        CourseTrackStudentPayment::create([
            'payment_date' => now(),
            'amount' => $course_track_student->courseTrackStudentPrice->final_price,
            'course_track_student_id' => $course_track_student->id,
            'payment_additional_amount' => 0,
            'comment' => intval($request->comment),
        ]);

        return response()->json("change successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * get attendance course track by course track id
     */
    public function studentAttendanceCourseTrack($course_track_id)
    {
        $course_track_students = CourseTrackStudent::with('lead')->where([
            ['course_track_id', $course_track_id],
            ['cancel', 0],
        ])->get();

        foreach ($course_track_students as $course_track_student) {
            $course_track_student->traineesAttendanceCourse;
            $course_track_student->attendance = 0;
            foreach ($course_track_student->traineesAttendanceCourse as $student) {
                if ($student->attendance == 1) {
                    $course_track_student->attendance = 1;
                } else {
                    $course_track_student->attendance = 0;
                }
            }
        }

        return response()->json($course_track_students);
    }
}
