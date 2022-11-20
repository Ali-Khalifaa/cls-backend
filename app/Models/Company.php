<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable  = [
        'name_en',
        'name_ar',
        'phone',
        'website',
        'pdf',
        'lead_source_id',
        'add_list',
        'add_placement',
        'is_client',
        'employee_id',
        'company_followup_id',
    ];

    protected $appends = [
        'file_path'
    ];

    //============== appends paths ===========

    //append img path

    public function getFilePathAttribute(): string
    {
        return asset('uploads/companies/'.$this->pdf);
    }

    //relation

    public function companyContacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyContact::class);
    }
    public function companyActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyActivity::class);
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }
    public function companyFollowup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CompanyFollowup::class,'company_followup_id');
    }
    public function leadSource(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeadSources::class,'lead_source_id');
    }

    public function dealIndividualPlacementTest(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealIndividualPlacementTest::class);
    }

    public function leads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function dealInterview(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealInterview::class);
    }
    public function companyPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyPayment::class);
    }

    public function companyInvoice(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyInvoice::class);
    }

    public function salesTeamPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SalesTeamPayment::class);
    }

}
