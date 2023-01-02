<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionType extends Model
{
    protected $fillable = ['title'];

    public function commissions ()
    {
        return $this->hasMany(Commission::class);
    }
}
