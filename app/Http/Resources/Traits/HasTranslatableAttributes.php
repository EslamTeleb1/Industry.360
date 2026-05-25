<?php

namespace App\Http\Resources\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslatableAttributes
{
    protected function translations(string $key): ?array
    {
        if (is_object($this->resource) && method_exists($this->resource, 'getTranslations')) {
            return $this->resource->getTranslations($key);
        }

        if (is_array($this->resource) && array_key_exists($key, $this->resource)) {
            return $this->resource[$key];
        }

        return null;
    }

    protected function translatedValue(string $key): ?string
    {
        $locale = App::getLocale();
        $allTranslations = $this->translations($key);

        if (is_array($allTranslations)) {
            return $allTranslations[$locale] ?? $allTranslations['en'] ?? null;
        }

        return $allTranslations;
    }
}
