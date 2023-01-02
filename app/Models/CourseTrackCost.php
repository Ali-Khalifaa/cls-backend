<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTrackCost extends Model
{
    protected $fillable = [
        'course_track_id',
        'price',
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

    public function courseTrack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CourseTrack::class,'course_track_id');
    }

}
