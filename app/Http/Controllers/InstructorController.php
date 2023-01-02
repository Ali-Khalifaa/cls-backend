<?php

namespace App\Http\Controllers;

use App\Imports\InstructorImport;
use App\Models\BankAccount;
use App\Models\CourseTrack;
use App\Models\CourseTrackSchedule;
use App\Models\DiplomaTrack;
use App\Models\DiplomaTrackSchedule;
use App\Models\Instructor;
use App\Models\InstructorPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InstructorController extends Controller
{

    /**
     * import data excel
     */
    public function instructorImport(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        Excel::import(new InstructorImport, $request->file('file')->store('temp'));

        $instructors = Instructor::where('img','=',null)->get();

        foreach ($instructors as $instructor)
        {
            $instructor->update([
                'img' =>'admin00100.png',
                'birth_date' =>'1990-01-15',
            ]);
        }

        return response()->json("successfully");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $instructors = Instructor::with(['user','bankAccount','trainingDiplomas','trainingCourses','availabilities'])->get();
        foreach ($instructors as $instructor) {
            $instructor->noAction = 0;
            if (count($instructor->trainingDiplomas) > 0 || count($instructor->trainingCourses) > 0){
                $instructor->noAction = 1;
            }
        }
        return response()->json($instructors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'mobile' => 'required|unique:instructors,mobile',
            'phone' => 'nullable|unique:instructors,phone',
            'email' => 'nullable|string|email|max:255|unique:instructors,email',
            'email_two' => 'nullable|string|email|max:255|unique:instructors,email',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|required|max:10000', // max 10000kb
            'cls_rate' => 'required|string',
            'has_account' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }

        //replase boolean
        $tempData = str_replace("", "", $request->has_account);

        if ($tempData == true) {
            $has_account = 1;
        } else {
            $has_account = 0;
        }

        //crete account
        if ($has_account == 1) {

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json($errors, 422);
            }

            $user = User::create([
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'type' => 'instructor'
            ]);
            $user_id = $user->id;
        } else {
            $user_id = null;
        }

        // image upload

        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $ext = $img->getClientOriginalExtension();
            $image_name = "instructor-image-" . uniqid() . ".$ext";
            $img->move(public_path('uploads/instructor/image/'), $image_name);
        }else{
            $image_name = null;
        }

        // pdf upload

        if ($request->hasFile('pdf')) {
            $img = $request->file('pdf');
            $ext = $img->getClientOriginalExtension();
            $pdf_name = "instructor-pdf-" . uniqid() . ".$ext";
            $img->move(public_path('uploads/instructor/pdf/'), $pdf_name);
        }else{
            $pdf_name = null;
        }

        $instructor = Instructor::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'phone' => $request->phone,
            'email' => $request->email,
            'email_two' => $request->email_two,
            'cls_rate' => $request->cls_rate,
            'cv' => $pdf_name,
            'img' => $image_name,
            'has_account' => $has_account,
            'user_id' => $user_id
        ]);

        if ($request->bank_id != "undefined" && $request->IBAN != "undefined" && $request->account_number != "undefined") {

            $bankaccount = BankAccount::create([
                'bank_id' => $request->bank_id,
                'instructor_id' => $instructor->id,
                'IBAN' => $request->IBAN,
                'account_number' => $request->account_number,
                'branch_name' => $request->branch_name,
            ]);
        }

        $instructor->availabilities()->syncWithoutDetaching($request->availabilities);

        return response()->json('created success');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $instructor = Instructor::with('user')->findOrFail($id);

        return response()->json($instructor);
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
            'name' => 'required|string|max:100',
            'mobile' => 'required|unique:instructors,mobile,'.$id,
            'phone' => 'nullable|unique:instructors,phone,'.$id,
            'email' => 'nullable|string|email|max:255|unique:instructors,email,'.$id,
            'email_two' => 'nullable|string|email|max:255|unique:instructors,email,'.$id,
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|required|max:10000', // max 10000kb
            'cls_rate' => 'required|string',
            'has_account' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }

        $instructor = Instructor::findOrFail($id);
        $img_name = $instructor->img;
        $pdf_name = $instructor->pdf;

        $request_data = $request->except(['has_account']);

        // image upload
        if ($request->image != "null" || $request->image != null) {
            if ($request->hasFile('image')) {
                $validator = Validator::make($request->all(), [
                    'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000', // max 10000kb
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return response()->json($errors, 422);
                }

                if (File::exists('uploads/instructor/image/' . $img_name) && $img_name != 'admin00100.png') {
                    unlink(public_path('uploads/instructor/image/') . $img_name);
                }

                $img = $request->file('image');
                $ext = $img->getClientOriginalExtension();
                $image_name = "instructor-image-" . uniqid() . ".$ext";
                $img->move(public_path('uploads/instructor/image/'), $image_name);
                $request_data['img'] = $image_name;
            }
        } else {
            $request_data['img'] = $img_name;
        }

        // c.v upload
        if ($request->pdf != "null" || $request->pdf != null) {
            if ($request->hasFile('pdf')) {

                $validator = Validator::make($request->all(), [
                    'pdf' => 'required|mimes:pdf|max:10000',
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return response()->json($errors, 422);
                }

                if (File::exists('uploads/instructor/pdf/' . $pdf_name)) {
                    unlink(public_path('uploads/instructor/pdf/') . $pdf_name);
                }

                $img = $request->file('pdf');
                $ext = $img->getClientOriginalExtension();
                $pdf_name = "instructor-pdf-" . uniqid() . ".$ext";
                $img->move(public_path('uploads/instructor/pdf/'), $pdf_name);
                $request_data['pdf'] = $pdf_name;
            }
        } else {
            $request_data['pdf'] = $pdf_name;
        }

        $instructor->update($request_data);

        $bankaccount = BankAccount::where('instructor_id', $id)->first();

        if ($bankaccount != null)
        {
            if ($request->bank_id != "undefined" && $request->IBAN != "undefined" && $request->account_number != "undefined") {

                $bankaccount->update([
                    'bank_id' => $request->bank_id,
                    'IBAN' => $request->IBAN,
                    'account_number' => $request->account_number,
                    'branch_name' => $request->branch_name,
                ]);
            }

        }else{

            if ($request->bank_id != "undefined" && $request->IBAN != "undefined" && $request->account_number != "undefined") {

                BankAccount::create([
                    'bank_id' => $request->bank_id,
                    'instructor_id' => $id,
                    'IBAN' => $request->IBAN,
                    'account_number' => $request->account_number,
                    'branch_name' => $request->branch_name,
                ]);
            }
        }

        $instructor->availabilities()->sync($request->availabilities);

        return response()->json('updated success');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);

        if ( count($instructor->trainingDiplomas) > 0 || count($instructor->trainingCourses) > 0 || count($instructor->interview) > 0 || $instructor->courseTrack > 0 || count($instructor->courseTrackSchedule) > 0 || $instructor->diplomaTrack > 0 || count($instructor->diplomaTrackSchedule) > 0 ) {
            return response()->json(0,422);
        }
        $instructor->delete();
        return response()->json("deleted successfully");
    }

    /**
     * Activation instructor.
     */

    public function activationInstructor($id)
    {

        $instructor = Instructor::findOrFail($id);
        if ($instructor->active == 1) {

            $instructor->update([
                'active' => 0,
            ]);

        } else {

            $instructor->update([
                'active' => 1,
            ]);
        }

        return response()->json($instructor);
    }

    /**
     * get Active instructors.
     */
    public function getActiveInstructor()
    {

        $instructor = Instructor::where('active', 1)->get();
        return response()->json($instructor);
    }

    /**
     * get des Active instructors.
     */
    public function getDeactivateInstructor()
    {
        $instructor = Instructor::where('active', 0)->get();
        return response()->json($instructor);
    }

    /**
     * Create Account Instructor.
     */
    public function createAccountInstructor(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }
        $instructor = Instructor::findOrFail($id);
        if ($instructor->has_account != 0) {
            return response()->json('this instructor has account', 422);
        }
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->get('password')),
            'type' => 'instructor'
        ]);

        $instructor->update([
            'has_account' => 1,
            'user_id' => $user->id
        ]);

        return response()->json($instructor);

    }

    /**
     * get course track Instructor.
     */
    public function courseTrackInstructor($id)
    {
        $course_track = CourseTrack::where('instructor_id', $id)->get();

        return response()->json($course_track);

    }

    /**
     * get diploma track Instructor.
     */
    public function diplomaTrackInstructor($id)
    {
        $diploma_track = DiplomaTrack::where('instructor_id', $id)->get();

        return response()->json($diploma_track);

    }

    /**
     * get lectures Instructor.
     */
    public function lecturesInstructor($id)
    {
        $data = [];
        $courses = CourseTrackSchedule::where('instructor_id', $id)->get();
        foreach ($courses as $course) {
            $course->name = $course->course_name;
            $data[] = $course;
        }

        $diplomas = DiplomaTrackSchedule::where('instructor_id', $id)->get();
        foreach ($diplomas as $diploma) {
            $diploma->name = $diploma->diploma_name;
            $data[] = $diploma;
        }
        return response()->json($data);
    }

    /**
     * get Latest Payments Instructor.
     */

    public function latestPaymentsInstructor($id)
    {

        $instructor_Payments = InstructorPayment::where([
            ['instructor_id', $id],
            ['treasury_id', '!=', null],
        ])->get();
        foreach ($instructor_Payments as $instructor_Payment) {
            if ($instructor_Payment->course_track_id != null) {
                $instructor_Payment->name = $instructor_Payment->courseTrack->name;
            }
            if ($instructor_Payment->diploma_track_id != null) {
                $instructor_Payment->name = $instructor_Payment->diplomaTrack->name;
            }
        }

        return response()->json($instructor_Payments);
    }

    /**
     * get Upcoming Payments Instructor.
     */

    public function upcomingPaymentsInstructor($id)
    {

        $data = [];
        $courseTracks = CourseTrack::where([
            ['cancel', 0],
            ['instructor_id', $id],
        ])->get();
        foreach ($courseTracks as $courseTrack) {
            $courseTrack->type = "course";
            $courseTrack->instructorPayment;
            $paiedAmount = 0;

            foreach ($courseTrack->instructorPayment as $instructor_payment) {
                if ($instructor_payment->treasury_id != null) {
                    $paiedAmount += $instructor_payment->amount;
                }
            }

            $courseTrack->paiedAmount = $paiedAmount;
            $courseTrack->hourPrice = $courseTrack->instructor->hour_price;
            $courseTrack->courseHoursAmount = $courseTrack->instructor_hour_cost;
            $absenselecturesCourse = $courseTrack->courseTrackSchedule->where('date', '<=', now())->count();
            $total_hours_dayle = 0;
            $attendance_start_time = $courseTrack->courseTrackSchedule[0]->start_time;
            $attendance_end_time = $courseTrack->courseTrackSchedule[0]->end_time;
            $start_time = strtotime($attendance_start_time);
            $end_time = strtotime($attendance_end_time);
            $totalSecondsDiff = abs($start_time - $end_time);
            $totalHoursDiff = $totalSecondsDiff / 60 / 60;
            $totalHoursInDay = ceil($totalHoursDiff);
            $total_hours_dayle = $totalHoursInDay;

            $attendanceHours = 0;
            $attendancelecturesCourse = 0;

            foreach ($courseTrack->instructor->instructorAttendance as $attendance) {
                if ($attendance->courseTrackSchedule != null) {
                    if ($attendance->courseTrackSchedule->course_track_id == $courseTrack->id) {
                        $attendancelecturesCourse += 0;
                        $attendance_start_time = $attendance->courseTrackSchedule->start_time;
                        $attendance_end_time = $attendance->courseTrackSchedule->end_time;
                        $start_time = strtotime($attendance_start_time);
                        $end_time = strtotime($attendance_end_time);
                        $totalSecondsDiff = abs($start_time - $end_time);
                        $totalHoursDiff = $totalSecondsDiff / 60 / 60;
                        $totalHoursInDay = ceil($totalHoursDiff);
                        $attendanceHours += $totalHoursInDay;
                    }
                }
            }

            $total_lectures = $absenselecturesCourse - $attendancelecturesCourse;
            $absenseHours = $total_lectures * $total_hours_dayle;
            $courseTrack->absenseHours = $absenseHours;
            $courseTrack->attendanceHours = $attendanceHours;
            $courseTrack->attendanceHoursAmount = $attendanceHours * $courseTrack->courseHoursAmount;
            $data[] = $courseTrack;
        }

        $diplomaTracks = DiplomaTrack::where([
            ['cancel', 0],
            ['instructor_id', $id],
        ])->get();

        foreach ($diplomaTracks as $diplomaTrack) {
            $diplomaTrack->type = "diploma";
            $diplomaTrack->instructorPayment;
            $paiedAmount = 0;
            foreach ($diplomaTrack->instructorPayment as $instructor_payment) {
                if ($instructor_payment->treasury_id != null) {
                    $paiedAmount += $instructor_payment->amount;
                }
            }
            $diplomaTrack->paiedAmount = $paiedAmount;
            $diplomaTrack->hourPrice = $diplomaTrack->instructor->hour_price;
            $diplomaTrack->courseHoursAmount = $diplomaTrack->instructor_hour_cost;
            $absenselecturesDiploma = $diplomaTrack->diplomaTrackSchedule->where('date', '<=', now())->count();
            $total_hours_dayle = 0;
            $attendance_start_time = $diplomaTrack->diplomaTrackSchedule[0]->start_time;
            $attendance_end_time = $diplomaTrack->diplomaTrackSchedule[0]->end_time;
            $start_time = strtotime($attendance_start_time);
            $end_time = strtotime($attendance_end_time);
            $totalSecondsDiff = abs($start_time - $end_time);
            $totalHoursDiff = $totalSecondsDiff / 60 / 60;
            $totalHoursInDay = ceil($totalHoursDiff);
            $total_hours_dayle = $totalHoursInDay;

            $attendanceHours = 0;
            $attendancelecturesDiploma = 0;

            foreach ($diplomaTrack->instructor->instructorAttendance as $attendance) {
                if ($attendance->diplomaTrackSchedule != null) {
                    if ($attendance->diplomaTrackSchedule->diploma_track_id == $diplomaTrack->id) {
                        $attendancelecturesDiploma += 0;
                        $attendance_start_time = $attendance->diplomaTrackSchedule->start_time;
                        $attendance_end_time = $attendance->diplomaTrackSchedule->end_time;
                        $start_time = strtotime($attendance_start_time);
                        $end_time = strtotime($attendance_end_time);
                        $totalSecondsDiff = abs($start_time - $end_time);
                        $totalHoursDiff = $totalSecondsDiff / 60 / 60;
                        $totalHoursInDay = ceil($totalHoursDiff);
                        $attendanceHours += $totalHoursInDay;
                    }
                }
            }

            $total_lectures = $absenselecturesDiploma - $attendancelecturesDiploma;
            $absenseHours = $total_lectures * $total_hours_dayle;
            $diplomaTrack->absenseHours = $absenseHours;
            $diplomaTrack->attendanceHours = $attendanceHours;
            $diplomaTrack->attendanceHoursAmount = $attendanceHours * $diplomaTrack->diplomaHoursAmount;
            $data[] = $diplomaTrack;
        }


        return response()->json($data);
    }

    /**
     * get lectures Instructor today.
     */

    public function lecturesInstructorToday($id)
    {
        $day = Carbon::now()->toDateString(); // Current date with Carbon
        $data = [];
        $courses = CourseTrackSchedule::where('instructor_id', $id)->whereDate('date', $day)->get();
        foreach ($courses as $course) {
            $course->name = $course->course_name;
            $course->type = "course";
            $data[] = $course;
        }

        $diplomas = DiplomaTrackSchedule::where('instructor_id', $id)->whereDate('date', $day)->get();
        foreach ($diplomas as $diploma) {
            $diploma->name = $diploma->diploma_name;
            $diploma->type = "diploma";
            $data[] = $diploma;
        }
        return response()->json($data);
    }

    /**
     * get student Instructor.
     */

    public function StudentInstructor($id)
    {
        $day = Carbon::now()->toDateString(); // Current date with Carbon
        $data = [];
        $course_tracks = CourseTrack::where('instructor_id', $id)->get();
        $index = 0;
        foreach ($course_tracks as $course_track) {
            if ($course_track->end_date >= $day) {
                foreach ($course_track->courseTrackStudent as $student) {
                    $data[$index]['first_name'] = $student->lead->first_name;
                    $data[$index]['id'] = $student->lead->id;
                    $data[$index]['middle_name'] = $student->lead->middle_name;
                    $data[$index]['last_name'] = $student->lead->last_name;
                    $data[$index]['email'] = $student->lead->email;
                    $data[$index]['phone'] = $student->lead->phone;
                    $data[$index]['mobile'] = $student->lead->mobile;
                    $data[$index]['name'] = $course_track->name;
                    $data[$index]['type'] = "course";
                    $index += 1;
                }
            }
        }

        $diploma_tracks = DiplomaTrack::where('instructor_id', $id)->get();
        foreach ($diploma_tracks as $diploma_track) {
            if ($diploma_track->end_date >= $day) {
                foreach ($diploma_track->diplomaTrackStudent as $student) {
                    $index += 1;
                    $data[$index]['id'] = $student->lead->id;
                    $data[$index]['first_name'] = $student->lead->first_name;
                    $data[$index]['middle_name'] = $student->lead->middle_name;
                    $data[$index]['last_name'] = $student->lead->last_name;
                    $data[$index]['email'] = $student->lead->email;
                    $data[$index]['phone'] = $student->lead->phone;
                    $data[$index]['mobile'] = $student->lead->mobile;
                    $data[$index]['name'] = $diploma_track->name;
                    $data[$index]['type'] = "course";
                }
            }
        }
        return response()->json($data);
    }

}
