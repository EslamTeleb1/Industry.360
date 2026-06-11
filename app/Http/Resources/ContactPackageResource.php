<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactPackageResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        $titleTranslations = $this->translations('title') ?? [];
        $descriptionTranslations = $this->translations('description') ?? [];

        return [
            'id' => $this->id,
            'title' => $this->translatedValue('title'),
            'title_en' => $titleTranslations['en'] ?? null,
            'title_ar' => $titleTranslations['ar'] ?? null,
            'description' => $this->translatedValue('description'),
            'description_en' => $descriptionTranslations['en'] ?? null,
            'description_ar' => $descriptionTranslations['ar'] ?? null,
            'contact_type' => $this->contact_type,
            'contact_id' => $this->contact_id,
            'order' => $this->order,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
