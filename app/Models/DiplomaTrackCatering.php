<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiplomaTrackCatering extends Model
{
    protected $fillable = [
        'diploma_track_id',
        'catering_id',
        'catering_price',
    ];

    //relations

    public function diplomaTrack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DiplomaTrack::class,'diploma_track_id');
    }

    public function catering(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Catering::class,'catering_id');
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DiplomaTrackStudent::class,'diploma_track_student_caterings','diploma_track_catering_id','diploma_track_student_id','id','id');
    }
}
