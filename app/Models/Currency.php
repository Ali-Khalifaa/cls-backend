<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable =['name'];

    public function trainingCourse()
    {
        return $this->hasMany(TrainingCourse::class);
    }

    public function trainingDiploma()
    {
        return $this->hasMany(TrainingDiploma::class);
    }
}
