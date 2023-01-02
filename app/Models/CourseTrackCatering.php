<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTrackCatering extends Model
{
    protected $fillable = [
        'course_track_id',
        'catering_id',
        'catering_price',
    ];

    //relations

    public function courseTrack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CourseTrack::class,'course_track_id');
    }

    public function catering(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Catering::class,'catering_id');
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(CourseTrackStudent::class,'course_track_student_caterings','course_track_catering_id','course_track_student_id','id','id');
    }
}
