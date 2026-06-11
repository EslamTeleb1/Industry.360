<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->translatedValue('title'),
            'title_en' => $this->getTranslation('title', 'en', false),
            'title_ar' => $this->getTranslation('title', 'ar', false),
            'position' => $this->translatedValue('position'),
            'position_en' => $this->getTranslation('position', 'en', false),
            'position_ar' => $this->getTranslation('position', 'ar', false),
            'link' => $this->link,
            'img_path' => $this->img_path,
            'img_url' => $this->img_url,
            'is_active' => $this->is_active,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
