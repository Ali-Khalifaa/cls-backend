<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyFollowup extends Model
{
    protected $fillable = [
        'name',
        'probability',
        'active',
    ];

    //relation

    public function companies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Company::class);
    }

}
