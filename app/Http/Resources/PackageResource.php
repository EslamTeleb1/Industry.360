<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    use HasTranslatableAttributes;
    public function toArray($request): array
    {
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
            'service' => new ServiceResource($this->whenLoaded('service')),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
