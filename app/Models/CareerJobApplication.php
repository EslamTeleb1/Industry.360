<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class CareerJobApplication extends Model
{
    protected $appends = [
        'cv_url',
    ];

    protected $fillable = [
        'career_job_id',
        'full_name',
        'email',
        'years_of_experience',
        'start_date',
        'expected_salary',
        'linkedin_profile',
        'cv_path',
        'cover_letter',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_salary' => 'decimal:2',
    ];

    public function job()
    {
        return $this->belongsTo(CareerJob::class, 'career_job_id');
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_path ? Storage::disk('public')->url($this->cv_path) : null;
    }
}
