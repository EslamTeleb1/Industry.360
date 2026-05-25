<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class CareerJob extends Model
{
    use HasTranslations;

    protected $appends = [
        'image_url',
    ];

    protected $fillable = [
        'department_id',
        'location_id',
        'job_type_id',
        'title',
        'description',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $translatable = ['title', 'description'];

    public function department()
    {
        return $this->belongsTo(CareerDepartment::class, 'department_id');
    }

    public function location()
    {
        return $this->belongsTo(CareerLocation::class, 'location_id');
    }

    public function jobType()
    {
        return $this->belongsTo(CareerJobType::class, 'job_type_id');
    }

    public function roleSections()
    {
        return $this->hasMany(CareerJobRoleSection::class, 'career_job_id')->orderBy('sort_order');
    }

    public function applications()
    {
        return $this->hasMany(CareerJobApplication::class, 'career_job_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}
