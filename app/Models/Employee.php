<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'mobile',
        'mobile_two',
        'email',
        'email_two',
        'job_id',
        'department_id',
        'pdf',
        'hiring_date',
        'date_of_resignation',
        'insurance_number',
        'ID_number',
        'birth_date',
        'military_id',
        'relation_status',
        'name_of_company_insurance',
        'salary',
        'img',
        'user_id',
        'branch_id',
        'has_account',
        'active',
        'admin',
    ];

    protected $appends = [
        'image_path'
    ];

    //============== appends paths ===========

    //append img path

    public function getImagePathAttribute(): string
    {
        return asset('uploads/employee/'.$this->img);
    }

    //append pdf path

    public function getPdfPathAttribute(): string
    {
        return asset('uploads/employee/'.$this->pdf);
    }

    //===============================================================

    //relations

    public function bankAccount(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BankAccount::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class,'department_id');
    }

    public function job(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Job::class,'job_id');
    }

    public function leadActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function leads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function companies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function dealIndividualPlacementTest(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealIndividualPlacementTest::class);
    }

    public function dealInterview(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DealInterview::class);
    }

    public function companyActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyActivity::class);
    }

    public function courseTrackStudent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackStudent::class);
    }

    public function courseTrackStudentComment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackStudentComment::class);
    }

    public function diplomaTrackStudent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrackStudent::class);
    }

    public function diplomaTrackStudentComment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrackStudentComment::class);
    }

    public function blackList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BlackList::class);
    }

    public function diplomaTrackStudentPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiplomaTrackStudentPayment::class);
    }

    public function courseTrackStudentPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourseTrackStudentPayment::class);
    }

    public function traineesPaymentSeals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TraineesPayment::class,'seals_man_id');
    }

    public function traineesPaymentAccountant(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TraineesPayment::class,'accountant_id');
    }

    public function incomeAndExpense(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IncomeAndExpense::class);
    }

    public function instructorPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InstructorPayment::class);
    }

    public function salesTeamPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SalesTeamPayment::class);
    }

    public function treasuryNotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TreasuryNotes::class);
    }

    public function transferringTreasury(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransferringTreasury::class);
    }

    public function salesTreasuryAccountant(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SalesTreasury::class,'employee_id');
    }

    public function salesTreasurySalesman(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SalesTreasury::class,'sales_man_id');
    }

    public function companyPayment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyPayment::class);
    }

    public function CompanyInvoiceSeals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyInvoice::class,'seals_man_id');
    }

    public function CompanyInvoiceAccountant(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyInvoice::class,'accountant_id');
    }

    public function assets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Asset::class,'employee_id');
    }

    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function commissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Commission::class,'commission_employees','employee_id','commission_id','id','id');
    }


}
