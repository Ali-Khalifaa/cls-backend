<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiplomaTrackMaterial extends Model
{
    protected $fillable =['diploma_track_id','material_id','material_price'];

    //relations

    public function diplomaTrack(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DiplomaTrack::class,'diploma_track_id');
    }

    public function material(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Material::class,'material_id');
    }
}
