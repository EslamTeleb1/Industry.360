<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactPackage extends Model
{
    use HasTranslations;

    protected $table = 'contact_packages';
    protected $fillable = ['title', 'description', 'contact_type', 'contact_id', 'order'];
    public $translatable = ['title', 'description'];
}
