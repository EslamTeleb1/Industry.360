<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    use HasTranslations;

    protected $fillable = [
        'blog_category_id',
        'title',
        'description',
        'date',
        'img_path',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'img_url',
    ];

    public $translatable = ['title', 'description'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function getImgUrlAttribute(): ?string
    {
        return $this->img_path ? asset('storage/' . $this->img_path) : null;
    }
}
