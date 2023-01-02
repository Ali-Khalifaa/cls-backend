<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'asset_id',
        'configuration_name',
        'configuration_value',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }
}
