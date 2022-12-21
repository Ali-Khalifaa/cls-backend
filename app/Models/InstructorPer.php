<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorPer extends Model
{
    protected $fillable = ['title'];

    public function trainingCourse()
    {
        return $this->hasMany(TrainingCourse::class);
    }

    public function trainingDiploma()
    {
        return $this->hasMany(TrainingDiploma::class);
    }
}
