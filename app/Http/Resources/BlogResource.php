<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'blog_category_id' => $this->blog_category_id,
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'title' => $this->getTranslations('title'),
            'description' => $this->getTranslations('description'),
            'date' => optional($this->date)->toDateString(),
            'img_path' => $this->img_path,
            'img_url' => $this->img_url,
            'is_active' => $this->is_active,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
