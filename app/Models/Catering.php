<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catering extends Model
{
    protected $fillable = [
        'name',
    ];

    public function courses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Course::class,'course_caterings','catering_id','course_id','id','id');
    }

    public function diplomas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Diploma::class,'diploma_caterings','catering_id','diploma_id','id','id');
    }

    public function courseTrack()
    {
        return $this->hasMany(CourseTrackCatering::class,'catering_id');
    }

    public function diplomaTrack()
    {
        return $this->hasMany(DiplomaTrackCatering::class,'catering_id');
    }
}
