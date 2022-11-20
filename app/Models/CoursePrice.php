<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePrice extends Model
{
    protected $fillable = [
        'course_id',
        'before_discount',
        'after_discount',
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

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Course::class,'course_id');
    }
}
