<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class VisionMessageResource extends JsonResource
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
            'img_path' => $this->img_path,
            'img_url' => $this->img_url,
            'percentage_title_1' => $this->translatedValue('percentage_title_1'),
            'percentage_title_1_en' => $this->getTranslation('percentage_title_1', 'en', false),
            'percentage_title_1_ar' => $this->getTranslation('percentage_title_1', 'ar', false),
            'percentage_value_1' => $this->percentage_value_1,
            'percentage_title_2' => $this->translatedValue('percentage_title_2'),
            'percentage_title_2_en' => $this->getTranslation('percentage_title_2', 'en', false),
            'percentage_title_2_ar' => $this->getTranslation('percentage_title_2', 'ar', false),
            'percentage_value_2' => $this->percentage_value_2,
            'percentage_title_3' => $this->translatedValue('percentage_title_3'),
            'percentage_title_3_en' => $this->getTranslation('percentage_title_3', 'en', false),
            'percentage_title_3_ar' => $this->getTranslation('percentage_title_3', 'ar', false),
            'percentage_value_3' => $this->percentage_value_3,
            'is_active' => $this->is_active,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
