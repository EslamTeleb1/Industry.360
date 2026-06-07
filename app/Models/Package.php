<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Package extends Model
{
    use HasTranslations;

    protected $fillable = [
        'service_id',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $translatable = ['title', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
