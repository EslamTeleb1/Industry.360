<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'percentage_value_1' => 'integer',
        'percentage_value_2' => 'integer',
        'percentage_value_3' => 'integer',
    ];

    public $translatable = [
        'title',
        'description',
        'sub_main_title',
        'sub_main_description',
        'sub_title',
        'sub_description',
        'percentage_title_1',
        'percentage_description_1',
        'percentage_title_2',
        'percentage_description_2',
        'percentage_title_3',
        'percentage_description_3',
    ];
}
