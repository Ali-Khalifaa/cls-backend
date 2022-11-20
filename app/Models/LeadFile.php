<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFile extends Model
{
    protected $fillable = [
      'lead_id',
      'name',
      'type',
      'file',
    ];

    protected $appends = [
        'file_path'
    ];

    //============== appends paths ===========

    //append img path

    public function getFilePathAttribute(): string
    {
        return asset('uploads/leads/'.$this->file);
    }

    //relations

    public function lead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lead::class,'lead_id');
    }
}
