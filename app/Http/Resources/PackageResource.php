<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    use HasTranslatableAttributes;
    public function toArray($request): array
    {
        $serviceType = $this->service_type ?? 'service';

        // Resolve the loaded relation — the controller loads by specific name
        $serviceable = match ($serviceType) {
            'contact_industry' => $this->whenLoaded('contactIndustry'),
            'contact_service'  => $this->whenLoaded('contactService'),
            'contact_solution' => $this->whenLoaded('contactSolution'),
            default            => $this->whenLoaded('service'),
        };

        // Fall back to the dynamic 'serviceable' relation (used by eager-load in index)
        if (! $serviceable instanceof \Illuminate\Database\Eloquent\Model) {
            $serviceable = $this->whenLoaded('serviceable');
        }

        $serviceResource = null;
        if ($serviceable instanceof \Illuminate\Database\Eloquent\Model) {
            $serviceResource = match ($serviceType) {
                'contact_industry', 'contact_service', 'contact_solution'
                    => new ContactLookupResource($serviceable),
                default => new ServiceResource($serviceable),
            };
        }

        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'service_type' => $serviceType,
            'title' => $this->translatedValue('title'),
            'title_en' => $this->getTranslation('title', 'en', false),
            'title_ar' => $this->getTranslation('title', 'ar', false),
            'description' => $this->translatedValue('description'),
            'description_en' => $this->getTranslation('description', 'en', false),
            'description_ar' => $this->getTranslation('description', 'ar', false),
            'is_active' => $this->is_active,
            'service' => $serviceResource,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
