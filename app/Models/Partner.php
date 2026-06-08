<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'img_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'img_url',
    ];

    public $translatable = ['title', 'subtitle', 'description'];

    public function getImgUrlAttribute(): ?string
    {
        return $this->img_path ? asset('storage/' . $this->img_path) : null;
    }
}
