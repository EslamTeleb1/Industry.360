<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CaseStudy extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'img_path',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tags' => 'array',
    ];

    protected $appends = [
        'img_url',
    ];

    public $translatable = ['title', 'description', 'tags'];

    public function getImgUrlAttribute(): ?string
    {
        return $this->img_path ? asset('storage/' . $this->img_path) : null;
    }
}
