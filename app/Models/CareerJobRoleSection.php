<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CareerJobRoleSection extends Model
{
    use HasTranslations;

    protected $fillable = [
        'career_job_id',
        'title',
        'description',
        'sort_order',
    ];

    public $translatable = ['title', 'description'];

    public function job()
    {
        return $this->belongsTo(CareerJob::class, 'career_job_id');
    }
}
