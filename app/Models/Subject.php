<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];

    public function leadActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function companyActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyActivity::class);
    }
}
