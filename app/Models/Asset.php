<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'branch_id',
        'date_of_purchasing',
        'color',
        'price',
        'serial_number',
        'pdf',
        'employee_id',
        'lab_id',
        'branch_id',
        'office_id'
    ];

    protected $appends = [
        'pdf_path',
    ];

    //append pdf path

    public function getPdfPathAttribute(): string
    {
        return asset('uploads/assets/pdf/'.$this->cv);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class,'lab_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class,'office_id');
    }

    public function configuration()
    {
        return $this->hasMany(Configuration::class);
    }

    public function software()
    {
        return $this->hasOne(Software::class);
    }
}
