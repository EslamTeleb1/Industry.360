<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\HasTranslatableAttributes;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    use HasTranslatableAttributes;

    public function toArray($request): array
    {
        $titleTranslations = $this->translations('title') ?? [];
        $descriptionTranslations = $this->translations('description') ?? [];

        return [
            'id' => $this->id,
            'blog_category_id' => $this->blog_category_id,
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'title' => $this->translatedValue('title'),
            'title_en' => $titleTranslations['en'] ?? null,
            'title_ar' => $titleTranslations['ar'] ?? null,
            'description' => $this->translatedValue('description'),
            'description_en' => $descriptionTranslations['en'] ?? null,
            'description_ar' => $descriptionTranslations['ar'] ?? null,
            'date' => optional($this->date)->toDateString(),
            'img_path' => $this->img_path,
            'img_url' => $this->img_url,
            'is_active' => $this->is_active,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
