<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseTrack;
use App\Models\CourseTrackCatering;
use App\Models\CourseTrackCost;
use App\Models\CourseTrackDay;
use App\Models\CourseTrackMaterial;
use App\Models\CourseTrackSchedule;
use App\Models\Day;
use App\Models\DiplomaTrackSchedule;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseTrackInitialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = CourseTrack::where('is_initial',1)
            ->with(['course','category','vendor','courseTrackCost','cateringTrack'=>function($q){
                $q->with('catering');
            },'materialTrack'=>function($qu){
                $qu->with('material');
            },'courseTrackDay','courseTrackSchedule'])->get();

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
            'course_id' => 'required|exists:courses,id',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'days' => 'required|array',
            'start_date' => 'required|date',
            'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'corporate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'private' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'online' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'protocol' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'corporate_group' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'official' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_cd' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_flash_memory' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'hard_copy' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'lab_virtual' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'membership_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'application_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'exam_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'block_note' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'pen' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'training_kit' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'catering' => 'required|array',
            'catering.*.catering_id' => 'required|exists:caterings,id',
            'catering.*.catering_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
//            'materials' => 'required|array',
//            'materials.*.material_id' => 'required|exists:materials,id',
//            'materials.*.material_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }
        //check date equal day
        $tempData = date('l', strtotime($request->start_date));
        $day_title = Day::where('day',$tempData)->first();

        if ( $day_title->id != $request->days[0])
        {
            return response()->json("This day is not equal date",422);
        }

        $lectures = $this->initialLectures($request);

        // start create

        $course_track = CourseTrack::create([
            'course_id' => $request->course_id,
            'category_id' => $request->category_id,
            'vendor_id' => $request->vendor_id,
            'start_date' => $request->start_date,
            'is_initial' => 1,
        ]);

        CourseTrackCost::create([
            'course_track_id' => $course_track->id,
            'price' => $request->price,
            'corporate' => $request->corporate,
            'private' => $request->private,
            'online' => $request->online,
            'protocol' => $request->protocol,
            'corporate_group' => $request->corporate_group,
            'official' => $request->official,
            'soft_copy_cd' => $request->soft_copy_cd,
            'soft_copy_flash_memory' => $request->soft_copy_flash_memory,
            'hard_copy' => $request->hard_copy,
            'lab_virtual' => $request->lab_virtual,
            'membership_price' => $request->membership_price,
            'application_price' => $request->application_price,
            'exam_price' => $request->exam_price,
            'block_note' => $request->block_note,
            'pen' => $request->pen,
            'training_kit' => $request->training_kit,
        ]);

        foreach ($request['catering'] as $catering){
            CourseTrackCatering::create([
                'course_track_id' => $course_track->id,
                'catering_id' => $catering['catering_id'],
                'catering_price' => $catering['catering_price'],
            ]);
        }

//        foreach ($request['materials'] as $material){
//            CourseTrackMaterial::create([
//                'course_track_id' => $course_track->id,
//                'material_id' => $material['material_id'],
//                'material_price' => $material['material_price'],
//            ]);
//        }

        foreach ($request->days as $course_day)
        {
            $day_title = Day::findOrFail($course_day);
            CourseTrackDay::create([
                'day' => $day_title->day,
                'course_track_id' => $course_track->id,
                'day_id' => $day_title->id,
            ]);
        }
        foreach ($lectures as $lecture){
            CourseTrackSchedule::create([
                'course_track_id' => $course_track->id,
                'course_id' => $request['course_id'],
                'day_id' => $lecture['day_id'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'date' => $lecture['date'],
                'active' => 0,
            ]);
        }
        $course_track_schedule = CourseTrackSchedule::where('course_track_id',$course_track->id)->get()->last();
        $course_track = CourseTrack::findOrFail($course_track->id);
        $course_track->update([
            'end_date' => $course_track_schedule->date
        ]);

        return response()->json($course_track);
    }

    public function initialLectures($request){
        $count_days_available = (count($request['days']));
        $course = Course::find($request['course_id']);
        $course_hours = ceil($course->hour_count);
        $start_time = strtotime($request['start_time'] );
        $end_time = strtotime($request['end_time']);
        $totalSecondsDiff = abs($start_time-$end_time);
        $totalHoursDiff   = $totalSecondsDiff/60/60;
        $totalHoursInDay = ceil($totalHoursDiff);
        $count_of_day = $course_hours / $totalHoursInDay;
        $number_of_weeks = $count_of_day / $count_days_available;
        $count_lectures = $number_of_weeks * $count_days_available;

        $lectures = [];
        $index = 0;
        $last_date = $request['start_date'];
        $last_day = 0;
        for ($i = 0 ; $i < ceil($number_of_weeks) ; $i++){
            foreach($request['days'] as $in => $day) {

                if ($i == 0 && $in ==0){
                    $lectures[$index]['day_id'] = $day;
                    $lectures[$index]['date'] = $request['start_date'];
                    $last_day = $day;
                    $index ++ ;
                }else{
                    if ($count_lectures == count($lectures)){
                        break;
                    }

                    $day_plus = $day - $last_day ;

                    if ($day_plus <= 0)
                    {
                        $day_plus = $day_plus + 7;
                    }

                    $date = date('Y-m-d', strtotime($last_date . ' + ' . $day_plus . ' days'));
                    $event = Event::whereDate('from_date','<=',$date)->whereDate('to_date','>=',$date)->first();
                    if ($event == null){
                        $last_date = $date;
                        $lectures[$index]['day_id'] = $day;
                        $lectures[$index]['date'] = $date;
                        $last_day = $day;
                        $index ++ ;
                    }else{
                        $last_date = $date;
                        $last_day = $day;
                    }
                }
            }
        }

        $lectures_waiting = $count_lectures - count($lectures) ;

        if ($lectures_waiting > 0){
            for ($i = 0 ; $i < ceil($lectures_waiting) ; $i++){

                foreach($request['days'] as $in => $day) {

                    if ($count_lectures == count($lectures)){
                        break;
                    }

                    $day_plus = $day - $last_day ;

                    if ($day_plus <= 0)
                    {
                        $day_plus = $day_plus + 7;
                    }

                    $date = date('Y-m-d', strtotime($last_date . ' + ' . $day_plus . ' days'));
                    $event = Event::whereDate('from_date','<=',$date)->whereDate('to_date','>=',$date)->first();
                    if ($event == null){
                        $last_date = $date;
                        $lectures[$index]['day_id'] = $day;
                        $lectures[$index]['date'] = $date;
                        $last_day = $day;
                        $index ++ ;
                    }else{
                        $last_date = $date;
                        $last_day = $day;
                    }

                }
            }
        }

        $lectures_waiting = $count_lectures - count($lectures) ;

        if ($lectures_waiting > 0){
            for ($i = 0 ; $i < ceil($lectures_waiting) ; $i++){

                foreach($request['days'] as $in => $day) {

                    if ($count_lectures == count($lectures)){
                        break;
                    }

                    $day_plus = $day - $last_day ;

                    if ($day_plus <= 0)
                    {
                        $day_plus = $day_plus + 7;
                    }

                    $date = date('Y-m-d', strtotime($last_date . ' + ' . $day_plus . ' days'));
                    $event = Event::whereDate('from_date','<=',$date)->whereDate('to_date','>=',$date)->first();
                    if ($event == null){
                        $last_date = $date;
                        $lectures[$index]['day_id'] = $day;
                        $lectures[$index]['date'] = $date;
                        $last_day = $day;
                        $index ++ ;
                    }else{
                        $last_date = $date;
                        $last_day = $day;
                    }

                }
            }
        }

        return $lectures;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $courses = CourseTrack::where('is_initial',1)
            ->with(['course','category','vendor','courseTrackCost','cateringTrack'=>function($q){
                $q->with('catering');
            },'materialTrack'=>function($qu){
                $qu->with('material');
            },'courseTrackDay','courseTrackSchedule'])->findOrFail($id);

        return response()->json($courses);
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
            'days' => 'required|array',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }

        //check date equal day

        $tempData = date('l', strtotime($request->start_date));
        $day_title = Day::where('day', $tempData)->first();
        if ($day_title->id != $request->days[0]) {
            return response()->json("This day is not equal date", 422);
        }

        $courseTrack = CourseTrack::find($id);
        $request_data = $request->all();
        $request_data['course_id'] = $courseTrack['course_id'];
        $lectures = $this->initialLectures($request_data);

        // start update

        $courseTrack->update([
            'start_date' => $request->start_date,
        ]);

        $course_track_days = CourseTrackDay::where('course_track_id', $id)->get();

        foreach ($course_track_days as $course_track_day) {
            $course_track_day->delete();
        }

        foreach ($request->days as $course_day) {
            $day_title = Day::findOrFail($course_day);
            CourseTrackDay::create([
                'day' => $day_title->day,
                'course_track_id' => $id,
                'day_id' => $day_title->id,
            ]);
        }

        $CourseTrackSchedules = CourseTrackSchedule::where('course_track_id', $id)->get();

        foreach ($CourseTrackSchedules as $CourseTrackSchedule)
        {
            $CourseTrackSchedule->delete();
        }

        foreach ($lectures as $lecture){
            CourseTrackSchedule::create([
                'course_track_id' => $courseTrack->id,
                'course_id' => $courseTrack['course_id'],
                'day_id' => $lecture['day_id'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'date' => $lecture['date'],
                'active' => 0,
            ]);
        }

        $course_track_schedule = CourseTrackSchedule::where('course_track_id',$id)->get()->last();
        $courseTrack->update([
            'end_date' => $course_track_schedule->date
        ]);

        return response()->json($courseTrack);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = CourseTrack::findOrFail($id);
        $course->delete();
        return response()->json('deleted success');
    }

    /**
     * chang initial course track price by course track id
     */

    public function updateInitialCoursePrice(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'corporate' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'private' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'online' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'protocol' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'corporate_group' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'official' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_cd' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'soft_copy_flash_memory' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'hard_copy' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'lab_virtual' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'membership_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'application_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'exam_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'block_note' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'pen' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'training_kit' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'catering' => 'required|array',
            'catering.*.catering_id' => 'required|exists:caterings,id',
            'catering.*.catering_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
//            'materials' => 'required|array',
//            'materials.*.material_id' => 'required|exists:materials,id',
//            'materials.*.material_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }

        $course_track = CourseTrack::findOrFail($id);

        $CourseTrackCost = CourseTrackCost::where('course_track_id',$id)->first();

        $CourseTrackCost->update([
            'price' => $request->price,
            'corporate' => $request->corporate,
            'private' => $request->private,
            'online' => $request->online,
            'protocol' => $request->protocol,
            'corporate_group' => $request->corporate_group,
            'official' => $request->official,
            'soft_copy_cd' => $request->soft_copy_cd,
            'soft_copy_flash_memory' => $request->soft_copy_flash_memory,
            'hard_copy' => $request->hard_copy,
            'lab_virtual' => $request->lab_virtual,
            'membership_price' => $request->membership_price,
            'application_price' => $request->application_price,
            'exam_price' => $request->exam_price,
            'block_note' => $request->block_note,
            'pen' => $request->pen,
            'training_kit' => $request->training_kit,
        ]);

        foreach ($request['catering'] as $catering){

            $course_catering = CourseTrackCatering::where([
                ['course_track_id',$id],
                ['catering_id',$catering['catering_id']],
            ])->first();
            if ($course_catering){
                $course_catering->update([
                    'catering_price' => $catering['catering_price'],
                ]);
            }else{
                CourseTrackCatering::create([
                    'course_track_id' => $course_track->id,
                    'catering_id' => $catering['catering_id'],
                    'catering_price' => $catering['catering_price'],
                ]);
            }
        }

//        foreach ($request['materials'] as $material){
//
//            $course_material = CourseTrackMaterial::where([
//                ['course_track_id',$id],
//                ['material_id',$material['material_id']],
//            ])->first();
//            if ($course_material){
//                $course_material->update([
//                    'material_price' => $material['material_price'],
//                ]);
//            }else{
//                CourseTrackMaterial::create([
//                    'course_track_id' => $course_track->id,
//                    'material_id' => $material['material_id'],
//                    'material_price' => $material['material_price'],
//                ]);
//            }
//        }

        return response()->json($course_track);

    }

    /**
     * get drop down initial course track by vendor id
     */

    public function dropdownsInitialCourse($id)
    {
        $course_tracks = CourseTrack::with(['course','category','vendor','courseTrackCost','cateringTrack'=>function($q){
                $q->with('catering');
            },'materialTrack'=>function($qu){
                $qu->with('material');
            },'courseTrackDay','courseTrackSchedule'])
        ->where([
            ['end_date','>=',now()],
            ['vendor_id','=',$id],
            ['is_initial',1],
        ])->get();

        return response()->json($course_tracks);
    }

}
