<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTrack extends Model
{
    protected $fillable = [
        'lab_id',
        'course_id',
        'instructor_id',
        'rate_per_hour',
        'delivery_type_id',
        'start_date',
        'end_date',
        'cancel',
        'is_initial',
        'category_id',
        'vendor_id',
    ];

    //appends

    protected $appends = [
        'trainees'
        ,'course_hours'
        ,'remaining_hours'
        ,'name'
        ,'days'
    ];

    //============== appends ===========

    public function getDaysAttribute()
    {
        return $this->courseTrackDay()->get('day_id')->pluck('day_id');
    }

    public function getCourseHoursAttribute()
    {
        return $this->course()->get('hour_count')->pluck('hour_count')->first();
    }

    public function getNameAttribute()
    {
        return $this->course()->get('name')->pluck('name')->first();
    }

    public function getRemainingHoursAttribute()
    {
        $schedules = $this->courseTrackSchedule()->get();
        $remainingtime = 0;
        foreach ($schedules as $schedule )
        {
            if ($schedule->date > now())
            {
                $start_time = strtotime($schedule->start_time );
                $end_time = strtotime($schedule->end_time);
                $totalSecondsDiff = abs($start_time-$end_time);
                $totalHoursDiff   = $totalSecondsDiff/60/60;
                $totalHoursInDay = ceil($totalHoursDiff);
                $remainingtime += $totalHoursInDay;
            }
        }
        return $remainingtime;
    }

    public function getTraineesAttribute(): string
    {
        return $this->courseTrackStudent()->where('cancel',0)->count();
    }

    //===========================================================================

    //relations

    public function lab(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lab::class,'lab_id');
    }

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Course::class,'course_id');
    }

    public function instructor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Instructor::class,'instructor_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function courseTrackCost(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackCost::class);
    }

    public function courseTrackSchedule(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackSchedule::class);
    }

    public function courseTrackDay(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackDay::class);
    }

    public function publicDiscount(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PublicDiscount::class);
    }

    public function courseTrackStudent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackStudent::class);
    }
    public function instructorPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InstructorPayment::class);
    }

    public function salesTeamPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SalesTeamPayment::class);
    }

    public function evaluationStudent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationStudent::class);
    }

    public function cateringTrack()
    {
        return $this->hasMany(CourseTrackCatering::class);
    }

    public function materialTrack()
    {
        return $this->hasMany(CourseTrackMaterial::class);
    }

}
