<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'email',
        'mobile',
        'Job_title',
        'birthday',
        'company_id',
    ];

    //relations

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class,'company_id');
    }
}
