<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'commission_type_id',
        'name',
        'per_id',
        'amount',
        'percentage',
    ];

    public function commissionType()
    {
        return $this->belongsTo(CommissionType::class,'commission_type_id');
    }

    public function per ()
    {
        return $this->belongsTo(Per::class,'per_id');
    }

    public function employees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Employee::class,'commission_employees','commission_id','employee_id','id','id');
    }
}
