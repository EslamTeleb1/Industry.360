<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    use HasTranslatableAttributes;
    public function toArray($request): array
    {
        $service = null;

          $serviceResource = null;
            $serviceable = $this->whenLoaded('serviceable');

            if ($serviceable) {
                $serviceType = $this->service_type ?? 'service';
                $serviceResource = match ($serviceType) {
                    'contact_industry', 'contact_service', 'contact_solution'
                        => new ContactLookupResource($serviceable),
                    default => new ServiceResource($serviceable),
                };
            }


        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'title' => $this->translatedValue('title'),
            'title_en' => $this->getTranslation('title', 'en', false),
            'title_ar' => $this->getTranslation('title', 'ar', false),
            'description' => $this->translatedValue('description'),
            'description_en' => $this->getTranslation('description', 'en', false),
            'description_ar' => $this->getTranslation('description', 'ar', false),
            'is_active' => $this->is_active,
            'service' =>$serviceResource,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
