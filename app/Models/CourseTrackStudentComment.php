<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTrackStudentComment extends Model
{
    protected $fillable = [
        'lead_id',
        'course_track_student_id',
        'employee_id',
        'comment',
    ];

    //relations

    public function lead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lead::class,'lead_id');
    }

    public function courseTrackStudent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CourseTrackStudent::class,'course_track_student_id');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
