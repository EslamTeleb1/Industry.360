<?php

namespace App\Models;

use App\Models\HomeSection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class HomeSetting extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'sub_main_title',
        'sub_main_description',
    ];

    protected $casts = [
        // percentage values moved to HomeSection model
    ];

    public $translatable = [
        'title',
        'description',
        'sub_main_title',
        'sub_main_description',
    ];

    /**
     * Sections module containing sub_title, percentages and image
     */
    public function sections()
    {
        return $this->hasMany(HomeSection::class);
    }
}
