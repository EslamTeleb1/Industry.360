<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\HomeSectionResource;

class HomeSettingResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            // Main section
            'title' => $this->translatedValue('title'),
            'title_en' => $this->getTranslation('title', 'en', false),
            'title_ar' => $this->getTranslation('title', 'ar', false),
            'description' => $this->translatedValue('description'),
            'description_en' => $this->getTranslation('description', 'en', false),
            'description_ar' => $this->getTranslation('description', 'ar', false),

            // Sub-main section
            'sub_main_title' => $this->translatedValue('sub_main_title'),
            'sub_main_title_en' => $this->getTranslation('sub_main_title', 'en', false),
            'sub_main_title_ar' => $this->getTranslation('sub_main_title', 'ar', false),
            'sub_main_description' => $this->translatedValue('sub_main_description'),
            'sub_main_description_en' => $this->getTranslation('sub_main_description', 'en', false),
            'sub_main_description_ar' => $this->getTranslation('sub_main_description', 'ar', false),

            // Sections module (each contains sub_title, descriptions, percentages, img)
            'sections' => HomeSectionResource::collection($this->whenLoaded('sections') ?? $this->sections),

            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
