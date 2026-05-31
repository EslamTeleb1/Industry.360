<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerJobTypeResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        $translations = $this->translations('name') ?? [];

        return [
            'id' => $this->id,
            'name' => $this->translatedValue('name'),
            'name_en' => $translations['en'] ?? null,
            'name_ar' => $translations['ar'] ?? null,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
