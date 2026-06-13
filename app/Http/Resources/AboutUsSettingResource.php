<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsSettingResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->translatedValue('title'),
            'title_en' => $this->getTranslation('title', 'en', false),
            'title_ar' => $this->getTranslation('title', 'ar', false),
            'description' => $this->translatedValue('description'),
            'description_en' => $this->getTranslation('description', 'en', false),
            'description_ar' => $this->getTranslation('description', 'ar', false),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
