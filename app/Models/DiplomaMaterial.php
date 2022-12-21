<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiplomaMaterial extends Model
{
    protected $fillable = ['diploma_price_id','material_id','material_price'];

    public function material(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Material::class,'material_id');
    }

    public function diplomaPrice(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DiplomaPrice::class,'diploma_price_id');
    }
}
