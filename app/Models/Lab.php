<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $fillable = [
        'name','branch_id','seats_capacity','pcs_capacity','active','area_dimensions'
    ];

    //relations

    public function courseTrack(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrack::class);
    }

    public function diplomaTrack(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrack::class);
    }

    public function courseTrackSchedule(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackSchedule::class);
    }

    public function diplomaTrackSchedule(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrackSchedule::class);
    }

    public function evaluationStudent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationStudent::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function assets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
