<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactService extends Model
{
    use HasTranslations;

    protected $table = 'contact_services';
    protected $fillable = ['title', 'description', 'img_path', 'order', 'is_active'];
    public $translatable = ['title', 'description'];

    protected $appends = ['img_url'];

    public function getImgUrlAttribute(): ?string
    {
        return $this->img_path ? asset('storage/' . $this->img_path) : null;
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'service_id')
            ->where('service_type', 'contact_service');
    }
}
