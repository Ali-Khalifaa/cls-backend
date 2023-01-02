<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadsFollowup extends Model
{
    protected $fillable = [
        'name',
        'active'
    ];

    //relation

    public function leads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class);
    }

}
