<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTrackMaterial extends Model
{
    protected $fillable = ['course_track_id','material_id','material_price'];

    //relations

    public function courseTrack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CourseTrack::class,'course_track_id');
    }

    public function material(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Material::class,'material_id');
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(CourseTrackStudent::class,'course_track_student_materials','course_track_material_id','course_track_student_id','id','id');
    }

}
