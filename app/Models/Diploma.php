<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diploma extends Model
{
    public $hidden=['pivot','catering_ids'];
    protected $fillable =[
      'name',
      'category_id',
      'vendor_id',
      'configuration_pcs',
      'active',
      'diploma_code',
    ];

    protected $appends = [
        'count_course',
        'course_hours',
        'catering_ids'
    ];

    //append Count Course

    public function getCountCourseAttribute()
    {
        return $this->courses()->count();
    }

    // append Course Hours

    public function getCourseHoursAttribute()
    {
        return $this->courses()->sum('hour_count');
    }

    //append catering id

    public function getCateringIdsAttribute()
    {
        return $this->caterings()->get()->pluck('id')->toArray();
    }

    //=============================================================================

    //relations

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function diplomaPrices(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DiplomaPrice::class);
    }

    public function courses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany('App\Models\Course','diploma_courses','diploma_id','course_id','id','id');
    }

    public function caterings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Catering::class,'diploma_caterings','diploma_id','catering_id','id','id');
    }

    public function traningDiplomas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrainingDiploma::class);
    }

    public function leadDiplomas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeadDiploma::class);
    }

    public function dealIndividualPlacementTest(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealIndividualPlacementTest::class);
    }

    public function exam(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function examDegrees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamDegree::class);
    }

    public function dealInterview(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealInterview::class);
    }

    public function interview(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function diplomaTrack(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrack::class);
    }

    public function diplomaTrackSchedule(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrackSchedule::class);
    }

    public function diplomaTrackStudent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrackStudent::class);
    }

}
