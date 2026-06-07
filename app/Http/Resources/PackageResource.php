<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'title' => $this->getTranslations('title'),
            'description' => $this->getTranslations('description'),
            'is_active' => $this->is_active,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
