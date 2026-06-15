<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class HomeSection extends Model
{
    use HasTranslations;

    protected $fillable = [
        'home_setting_id',
        'sub_title',
        'sub_description',
        'percentage_title_1',
        'percentage_description_1',
        'percentage_value_1',
        'percentage_title_2',
        'percentage_description_2',
        'percentage_value_2',
        'percentage_title_3',
        'percentage_description_3',
        'percentage_value_3',
        'img',
    ];

    protected $casts = [
        'percentage_value_1' => 'integer',
        'percentage_value_2' => 'integer',
        'percentage_value_3' => 'integer',
    ];

    protected $appends = [
        'img_url',
    ];

    public $translatable = [
        'sub_title',
        'sub_description',
        'percentage_title_1',
        'percentage_description_1',
        'percentage_title_2',
        'percentage_description_2',
        'percentage_title_3',
        'percentage_description_3',
    ];

    public function homeSetting()
    {
        return $this->belongsTo(HomeSetting::class);
    }

    public function getImgUrlAttribute(): ?string
    {
        return $this->img ? asset('storage/' . $this->img) : null;
    }
}
