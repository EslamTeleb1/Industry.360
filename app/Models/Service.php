<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations;

    protected $appends = [
        'img_url',
    ];

    protected $fillable = [
        'type',
        'title',
        'description',
        'img_path',
        'service_order',
    ];

    protected $casts = [
        'service_order' => 'integer',
    ];

    public $translatable = ['title', 'description'];

    public function getImgUrlAttribute(): ?string
    {
        return $this->img_path ? asset('storage/' . $this->img_path) : null;
    }
}