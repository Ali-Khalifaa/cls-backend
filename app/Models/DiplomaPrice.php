<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiplomaPrice extends Model
{

    protected $fillable = [
        'diploma_id',
        'before_discount',
        'after_discount',
        'corporate',
        'private',
        'online',
        'protocol',
        'corporate_group',
        'official',
        'soft_copy_cd',
        'soft_copy_flash_memory',
        'hard_copy',
        'lab_virtual',
        'membership_price',
        'application_price',
        'exam_price',
        'block_note',
        'pen',
        'training_kit',
        'from_date',
        'active_date',
    ];

    //relations

    public function diploma(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Diploma::class,'diploma_id');
    }

    public function materials()
    {
        return $this->hasMany(DiplomaMaterial::class);
    }
}
