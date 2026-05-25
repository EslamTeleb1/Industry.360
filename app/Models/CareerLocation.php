<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CareerLocation extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
    ];

    public $translatable = ['name'];

    public function jobs()
    {
        return $this->hasMany(CareerJob::class, 'location_id');
    }
}
