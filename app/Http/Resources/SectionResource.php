<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->translatedValue('sub_title'),
            'title_en' => $this->getTranslation('sub_title', 'en', false),
            'title_ar' => $this->getTranslation('sub_title', 'ar', false),
            'sub_title' => $this->translatedValue('sub_title'),
            'sub_title_en' => $this->getTranslation('sub_title', 'en', false),
            'sub_title_ar' => $this->getTranslation('sub_title', 'ar', false),

            'description' => $this->translatedValue('sub_description'),
            'description_en' => $this->getTranslation('sub_description', 'en', false),
            'description_ar' => $this->getTranslation('sub_description', 'ar', false),
            'sub_description' => $this->translatedValue('sub_description'),
            'sub_description_en' => $this->getTranslation('sub_description', 'en', false),
            'sub_description_ar' => $this->getTranslation('sub_description', 'ar', false),

            'percentage_title_1' => $this->translatedValue('percentage_title_1'),
            'percentage_title_1_en' => $this->getTranslation('percentage_title_1', 'en', false),
            'percentage_title_1_ar' => $this->getTranslation('percentage_title_1', 'ar', false),
            'percentage_description_1' => $this->translatedValue('percentage_description_1'),
            'percentage_description_1_en' => $this->getTranslation('percentage_description_1', 'en', false),
            'percentage_description_1_ar' => $this->getTranslation('percentage_description_1', 'ar', false),
            'percentage_value_1' => $this->percentage_value_1,

            'percentage_title_2' => $this->translatedValue('percentage_title_2'),
            'percentage_title_2_en' => $this->getTranslation('percentage_title_2', 'en', false),
            'percentage_title_2_ar' => $this->getTranslation('percentage_title_2', 'ar', false),
            'percentage_description_2' => $this->translatedValue('percentage_description_2'),
            'percentage_description_2_en' => $this->getTranslation('percentage_description_2', 'en', false),
            'percentage_description_2_ar' => $this->getTranslation('percentage_description_2', 'ar', false),
            'percentage_value_2' => $this->percentage_value_2,

            'percentage_title_3' => $this->translatedValue('percentage_title_3'),
            'percentage_title_3_en' => $this->getTranslation('percentage_title_3', 'en', false),
            'percentage_title_3_ar' => $this->getTranslation('percentage_title_3', 'ar', false),
            'percentage_description_3' => $this->translatedValue('percentage_description_3'),
            'percentage_description_3_en' => $this->getTranslation('percentage_description_3', 'en', false),
            'percentage_description_3_ar' => $this->getTranslation('percentage_description_3', 'ar', false),
            'percentage_value_3' => $this->percentage_value_3,

            'img' => $this->img,
            'img_url' => $this->img_url,

            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
