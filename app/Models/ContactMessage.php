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
        'description',
    ];

    public function industry()
    {
        return $this->belongsTo(Service::class, 'industry_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function solution()
    {
        return $this->belongsTo(Service::class, 'solution_id');
    }
}
