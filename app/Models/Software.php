<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    protected $fillable = [
        'asset_id',
        'name',
        'end_date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }
}
