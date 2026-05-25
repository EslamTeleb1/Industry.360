<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerJobResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'department' => new CareerDepartmentResource($this->whenLoaded('department')),
            'location' => new CareerLocationResource($this->whenLoaded('location')),
            'job_type' => new CareerJobTypeResource($this->whenLoaded('jobType')),
            'title' => $this->translatedValue('title'),
            'description' => $this->translatedValue('description'),
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'role_sections' => CareerJobRoleSectionResource::collection($this->whenLoaded('roleSections')),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
