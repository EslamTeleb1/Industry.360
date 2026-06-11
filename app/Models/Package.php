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
        'service_type',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $translatable = ['title', 'description'];

    /**
     * Get the polymorphic service/lookup relationship
     */
    public function serviceable()
    {
        return match ($this->service_type) {
            'contact_industry' => $this->belongsTo(ContactIndustry::class, 'service_id'),
            'contact_service' => $this->belongsTo(ContactService::class, 'service_id'),
            'contact_solution' => $this->belongsTo(ContactSolution::class, 'service_id'),
            default => $this->belongsTo(Service::class, 'service_id'),
        };
    }

    /**
     * Service relationship (for backward compatibility)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function contactIndustry(): BelongsTo
    {
        return $this->belongsTo(ContactIndustry::class, 'service_id');
    }

    public function contactService(): BelongsTo
    {
        return $this->belongsTo(ContactService::class, 'service_id');
    }

    public function contactSolution(): BelongsTo
    {
        return $this->belongsTo(ContactSolution::class, 'service_id');
    }
}
