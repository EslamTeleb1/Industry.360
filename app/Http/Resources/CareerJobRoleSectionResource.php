<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerJobRoleSectionResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->translatedValue('title'),
            'description' => $this->translatedValue('description'),
            'sort_order' => $this->sort_order,
        ];
    }
}
