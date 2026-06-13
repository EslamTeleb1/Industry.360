<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TeamMember extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'position',
        'tag',
        'description',
        'social_links',
        'img_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'social_links' => 'array',
    ];

    protected $appends = [
        'img_url',
    ];

    public $translatable = ['title', 'position', 'description'];

    public function getImgUrlAttribute(): ?string
    {
        return $this->img_path ? asset('storage/' . $this->img_path) : null;
    }
}
