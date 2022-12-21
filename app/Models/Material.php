<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name'];

    public function courseMaterial()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function diplomaMaterial()
    {
        return $this->hasMany(DiplomaMaterial::class);
    }

    public function courseTracks()
    {
        return $this->hasMany(CourseTrackMaterial::class);
    }

    public function diplomaTracks()
    {
        return $this->hasMany(DiplomaTrackMaterial::class);
    }
}
