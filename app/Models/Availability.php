<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = ['title'];

    public function instructors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Instructor::class,'instructor_availabilities','availability_id','instructor_id','id','id');
    }
}
