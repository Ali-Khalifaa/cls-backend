<?php

namespace App\Http\Controllers;

use App\Models\CourseTrack;
use App\Models\CourseTrackSchedule;
use App\Models\Day;
use App\Models\DiplomaTrackSchedule;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseTrackInitialScheduleController extends Controller
{
    /**
     * get initial schedule by course track id
     */
    public function getInitialScheduleByCourseTrackId($id)
    {
        $Course_tracks = CourseTrackSchedule::with(['course','day','courseTrack'])->where('course_track_id',$id)->get();
        $days =[];
        foreach ($Course_tracks as $Course_track)
        {

            $Course_track->courseTrack->courseTrackDay;
            foreach ( $Course_track->courseTrack->courseTrackDay as $day)
            {
                $days[]= "$day->day_id";
            }
            $Course_track->days = $days;
        }

        return response()->json($Course_tracks);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedule = CourseTrackSchedule::where('date','>=',now())->get();
        return response()->json($schedule);
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
            'course_track_id' => 'required|exists:course_tracks,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $event = Event::whereDate('from_date','<=',$request->date)->whereDate('to_date','>=',$request->date)->first();

        if ($event != null)
        {
            return response()->json("This time is reserved",422);
        }

        $tempData = date('l', strtotime($request->date));
        $day_title = Day::where('day',$tempData)->first();

        $course_track = CourseTrack::findOrFail($request->course_track_id);

        $course_schedule = CourseTrackSchedule::create([
            'course_track_id' => $request->course_track_id,
            'course_id' =>   $course_track->course->id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
            'day_id' => $day_title->id,
            'active' => 0,
        ]);

        return response()->json($course_schedule);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json($errors,422);
        }

        $event = Event::whereDate('from_date','<=',$request->date)->whereDate('to_date','>=',$request->date)->first();
        if ($event != null)
        {
            return response()->json("This time is reserved",422);
        }

        $tempData = date('l', strtotime($request->date));
        $day_title = Day::where('day',$tempData)->first();

        $course_schedule = CourseTrackSchedule::findOrFail($id);

        $course_schedule->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
            'day_id' => $day_title->id,
        ]);

        return response()->json($course_schedule);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course_schedule = CourseTrackSchedule::findOrFail($id);
        $course_schedule->delete();

        return response()->json("deleted successfully");
    }
}
