<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiplomaTrackStudentPrice extends Model
{
    protected $fillable = [
        'diploma_track_student_id',
        'final_price',
        'total_discount',
        'course_price',
        'corporate',
        'private',
        'online',
        'protocol',
        'corporate_group',
        'official',
        'soft_copy_cd',
        'soft_copy_flash_memory',
        'hard_copy',
        'lab_virtual',
        'membership_price',
        'application_price',
        'exam_price',
        'block_note',
        'pen',
        'training_kit',
    ];

    //relations

    public function diplomaTrackStudent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DiplomaTrackStudent::class,'diploma_track_student_id');
    }
}
