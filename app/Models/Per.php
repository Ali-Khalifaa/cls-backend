<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Per extends Model
{
    protected $fillable = ['month'];

    public function commissions ()
    {
        return $this->hasMany(Commission::class);
    }
}
