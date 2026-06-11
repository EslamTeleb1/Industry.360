<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'full_name',
        'company_name',
        'email',
        'phone',
        'industry_id',
        'service_id',
        'solution_id',
        'package_id',
        'description',
    ];

    public function industry()
    {
        return $this->belongsTo(ContactIndustry::class, 'industry_id');
    }

    public function service()
    {
        return $this->belongsTo(ContactService::class, 'service_id');
    }

    public function solution()
    {
        return $this->belongsTo(ContactSolution::class, 'solution_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
