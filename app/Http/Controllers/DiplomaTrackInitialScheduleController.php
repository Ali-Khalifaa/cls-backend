<?php

namespace App\Http\Controllers;

use App\Models\CourseTrackSchedule;
use App\Models\Day;
use App\Models\DiplomaTrack;
use App\Models\DiplomaTrackSchedule;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiplomaTrackInitialScheduleController extends Controller
{
    /**
     * get initial schedule by diploma track id
     */
    public function getInitialScheduleByDiplomaTrackId($id)
    {
        $Course_tracks = DiplomaTrackSchedule::with(['diploma','day','diplomaTrack'])->where('diploma_track_id',$id)->get();
        $days =[];
        foreach ($Course_tracks as $Course_track)
        {

            $Course_track->diplomaTrack->diplomaTrackDay;
            foreach ( $Course_track->diplomaTrack->diplomaTrackDay as $day)
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
        $schedule = DiplomaTrackSchedule::where('date','>=',now())->get();

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
            'diploma_track_id' => 'required|exists:diploma_tracks,id',
            'course_id' => 'required|exists:courses,id',
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

        $tempData = date('l', strtotime($request->start_date));
        $day_title = Day::where('day',$tempData)->first();

        $diploma_track = DiplomaTrack::findOrFail($request->diploma_track_id);

        $course_schedule = DiplomaTrackSchedule::create([
            'course_id' => $request->course_id,
            'diploma_track_id' => $request->diploma_track_id,
            'diploma_id' => $diploma_track->diploma->id,
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


        $tempData = date('l', strtotime($request->start_date));
        $day_title = Day::where('day',$tempData)->first();

        $course_schedule = DiplomaTrackSchedule::findOrFail($id);

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
        $course_schedule = DiplomaTrackSchedule::findOrFail($id);
        $course_schedule->delete();

        return response()->json("deleted successfully");
    }

}
