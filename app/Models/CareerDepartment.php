<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CareerDepartment extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
    ];

    public $translatable = ['name'];

    public function jobs()
    {
        return $this->hasMany(CareerJob::class, 'department_id');
    }
}
