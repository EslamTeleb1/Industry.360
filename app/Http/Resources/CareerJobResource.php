<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerJobResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        $titleTranslations = $this->translations('title') ?? [];
        $descriptionTranslations = $this->translations('description') ?? [];

        return [
            'id' => $this->id,
            'department' => new CareerDepartmentResource($this->whenLoaded('department')),
            'location' => new CareerLocationResource($this->whenLoaded('location')),
            'job_type' => new CareerJobTypeResource($this->whenLoaded('jobType')),
            'title' => $this->translatedValue('title'),
            'title_en' => $titleTranslations['en'] ?? null,
            'title_ar' => $titleTranslations['ar'] ?? null,
            'description' => $this->translatedValue('description'),
            'description_en' => $descriptionTranslations['en'] ?? null,
            'description_ar' => $descriptionTranslations['ar'] ?? null,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'role_sections' => CareerJobRoleSectionResource::collection($this->whenLoaded('roleSections')),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
