<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class VisionMessage extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'img_path',
        'percentage_title_1',
        'percentage_value_1',
        'percentage_title_2',
        'percentage_value_2',
        'percentage_title_3',
        'percentage_value_3',
        'is_active',
    ];

    protected $casts = [
        'percentage_value_1' => 'integer',
        'percentage_value_2' => 'integer',
        'percentage_value_3' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'img_url',
    ];

    public $translatable = [
        'title',
        'description',
        'percentage_title_1',
        'percentage_title_2',
        'percentage_title_3',
    ];

    public function getImgUrlAttribute(): ?string
    {
        return $this->img_path ? asset('storage/' . $this->img_path) : null;
    }
}
