<?php

namespace App\Http\Controllers;

use App\Models\CourseTrackSchedule;
use App\Models\Day;
use App\Models\Diploma;
use App\Models\DiplomaTrack;
use App\Models\DiplomaTrackCatering;
use App\Models\DiplomaTrackCost;
use App\Models\DiplomaTrackDay;
use App\Models\DiplomaTrackMaterial;
use App\Models\DiplomaTrackSchedule;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiplomaTrackInitialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $diploma_tracks = DiplomaTrack::where('is_initial',1)
            ->with(['diploma','category','vendor','diplomaTrackCost','diplomaTrackDay','diplomaTrackSchedule'=>function($q){
                $q->with('day');
            },'cateringTrack'=>function($q){
                $q->with('catering');
            }])->get();

        return response()->json($diploma_tracks);
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
            'diploma_id' => 'required|exists:diplomas,id',
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

        $course_track = DiplomaTrack::create([
            'diploma_id' => $request->diploma_id,
            'category_id' => $request->category_id,
            'vendor_id' => $request->vendor_id,
            'start_date' => $request->start_date,
            'is_initial' => 1,
        ]);

        DiplomaTrackCost::create([
            'diploma_track_id' => $course_track->id,
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
            DiplomaTrackCatering::create([
                'diploma_track_id' => $course_track->id,
                'catering_id' => $catering['catering_id'],
                'catering_price' => $catering['catering_price'],
            ]);
        }
//
//        foreach ($request['materials'] as $material){
//            DiplomaTrackMaterial::create([
//                'diploma_track_id' => $course_track->id,
//                'material_id' => $material['material_id'],
//                'material_price' => $material['material_price'],
//            ]);
//        }

        foreach ($request->days as $course_day)
        {
            $day_title = Day::findOrFail($course_day);
            DiplomaTrackDay::create([
                'day' => $day_title->day,
                'diploma_track_id' => $course_track->id,
                'day_id' => $day_title->id,
            ]);
        }

        foreach ($lectures as $lecture){
            DiplomaTrackSchedule::create([
                'diploma_track_id' => $course_track->id,
                'diploma_id' => $request['diploma_id'],
                'course_id' => $lecture['course_id'],
                'day_id' => $lecture['day_id'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'date' => $lecture['date'],
                'active' => 0,
            ]);
        }

        $course_track_schedule = DiplomaTrackSchedule::where('diploma_track_id',$course_track->id)->get()->last();

        $course_track->update([
            'end_date' => $course_track_schedule->date
        ]);

        return response()->json($course_track);
    }

    public function initialLectures($request){

        $count_days_available = (count($request['days']));
        $diploma = Diploma::find($request['diploma_id']);
        $course_hours = ceil($diploma->course_hours);
        $start_time = strtotime($request['start_time']);
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
                    $lectures[$index]['course_id'] = null;
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
                        $lectures[$index]['course_id'] = null;
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
                        $lectures[$index]['course_id'] = null;
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
                        $lectures[$index]['course_id'] = null;
                        $last_day = $day;
                        $index ++ ;
                    }else{
                        $last_date = $date;
                        $last_day = $day;
                    }

                }
            }
        }

        $index_course_id = 0;

        foreach ($diploma->courses as $course){
            $course_hour = $course->hour_count;
            foreach ($lectures as $lec){
                if ($course_hour > 0){
                    if ($lec['course_id'] == null)
                    {
                        $lectures[$index_course_id]['course_id']= $course->id;
                        $course_hour-=$totalHoursDiff;
                        $index_course_id+=1;
                    }
                }else{
                    break;
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
        $diploma_track = DiplomaTrack::with(['diploma','category','vendor','diplomaTrackCost','diplomaTrackDay','diplomaTrackSchedule'=>function($q){
            $q->with('day');
        },'cateringTrack'=>function($q){
            $q->with('catering');
        },'materialTrack'=>function($q){
            $q->with('material');
        }])->findOrFail($id);

        return response()->json($diploma_track);
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

        $courseTrack = DiplomaTrack::find($id);
        $request_data = $request->all();
        $request_data['diploma_id'] = $courseTrack['diploma_id'];
        $lectures = $this->initialLectures($request_data);

        // start update

        $courseTrack->update([
            'start_date' => $request->start_date,
        ]);

        $course_track_days = DiplomaTrackDay::where('diploma_track_id', $id)->get();

        foreach ($course_track_days as $course_track_day) {
            $course_track_day->delete();
        }

        foreach ($request->days as $course_day) {
            $day_title = Day::findOrFail($course_day);
            DiplomaTrackDay::create([
                'day' => $day_title->day,
                'diploma_track_id' => $id,
                'day_id' => $day_title->id,
            ]);
        }

        $CourseTrackSchedules = DiplomaTrackSchedule::where('diploma_track_id', $id)->get();

        foreach ($CourseTrackSchedules as $CourseTrackSchedule)
        {
            $CourseTrackSchedule->delete();
        }

        foreach ($lectures as $lecture){
            DiplomaTrackSchedule::create([
                'diploma_track_id' => $courseTrack->id,
                'diploma_id' => $request['diploma_id'],
                'day_id' => $lecture['day_id'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'date' => $lecture['date'],
                'active' => 0,
            ]);
        }

        $course_track_schedule = DiplomaTrackSchedule::where('diploma_track_id',$id)->get()->last();
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
        $course = DiplomaTrack::findOrFail($id);
        $course->delete();
        return response()->json('deleted success');
    }

    /**
     * chang initial diploma track price by diploma track id
     */

    public function updateInitialDiplomaPrice(Request $request,$id)
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
//            'catering' => 'required|array',
//            'catering.*.catering_id' => 'required|exists:caterings,id',
//            'catering.*.catering_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
//            'materials' => 'required|array',
//            'materials.*.material_id' => 'required|exists:materials,id',
//            'materials.*.material_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors, 422);
        }

        $course_track = DiplomaTrack::findOrFail($id);

        $CourseTrackCost = DiplomaTrackCost::where('diploma_track_id',$id)->first();

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

//        foreach ($request['catering'] as $catering){
//
//            $course_catering = DiplomaTrackCatering::where([
//                ['diploma_track_id',$id],
//                ['catering_id',$catering['catering_id']],
//            ])->first();
//            if ($course_catering){
//                $course_catering->update([
//                    'catering_price' => $catering['catering_price'],
//                ]);
//            }else{
//                DiplomaTrackCatering::create([
//                    'diploma_track_id' => $course_track->id,
//                    'catering_id' => $catering['catering_id'],
//                    'catering_price' => $catering['catering_price'],
//                ]);
//            }
//        }

//        foreach ($request['materials'] as $material){
//
//            $diploma_catering = DiplomaTrackMaterial::where([
//                ['diploma_track_id',$id],
//                ['material_id',$material['material_id']],
//            ])->first();
//            if ($diploma_catering){
//                $diploma_catering->update([
//                    'material_price' => $material['material_price'],
//                ]);
//            }else{
//                DiplomaTrackMaterial::create([
//                    'diploma_track_id' => $course_track->id,
//                    'material_id' => $material['material_id'],
//                    'material_price' => $material['material_price'],
//                ]);
//            }
//        }

        return response()->json($course_track);
    }

    //get drop down initial diploma track by vendor id

    public function dropdownsInitialDiploma($id)
    {
        $course_tracks = DiplomaTrack::with(['diploma','category','vendor','diplomaTrackCost','diplomaTrackDay','diplomaTrackSchedule'=>function($q){
            $q->with('day');
        },'cateringTrack'=>function($q){
            $q->with('catering');
        },'materialTrack'=>function($q){
            $q->with('material');
        }])->where([
            ['end_date','>=',now()],
            ['vendor_id','=',$id],
            ['is_initial',1],
        ])->get();

        return response()->json($course_tracks);
    }
}
