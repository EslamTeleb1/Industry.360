<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class CareersPageSetting extends Model
{
    use HasTranslations;

    protected $appends = [
        'banner_image_url',
    ];

    protected $fillable = [
        'description',
        'banner_image_path',
    ];

    public $translatable = ['description'];

    public function getBannerImageUrlAttribute(): ?string
    {
        return $this->banner_image_path ? Storage::disk('public')->url($this->banner_image_path) : null;
    }
}
