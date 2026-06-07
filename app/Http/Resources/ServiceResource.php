<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        $titleTranslations = $this->translations('title') ?? [];
        $descriptionTranslations = $this->translations('description') ?? [];

        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->translatedValue('title'),
            'title_en' => $titleTranslations['en'] ?? null,
            'title_ar' => $titleTranslations['ar'] ?? null,
            'description' => $this->translatedValue('description'),
            'description_en' => $descriptionTranslations['en'] ?? null,
            'description_ar' => $descriptionTranslations['ar'] ?? null,
            'img_url' => $this->img_url,
            'service_order' => $this->service_order,
            'packages' => PackageResource::collection($this->whenLoaded('packages')),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
