<?php

namespace App\Traits;

use App\Models\Service;
use Illuminate\Http\Request;

trait IndustryModuleHandler
{
    /**
     * Get query builder for a specific industry type
     */
    protected function typeQuery(string $type)
    {
        return Service::query()
            ->where('type', $type)
            ->orderBy('service_order')
            ->orderBy('id');
    }

    /**
     * Validate industry item data
     */
    protected function validateIndustryItem(Request $request, bool $isCreate = true): array
    {
        return $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'img' => [$isCreate ? 'required' : 'nullable', 'image', 'max:2048'],
            'service_order' => ['required', 'integer', 'min:1'],
        ]);
    }

    /**
     * Get the module type for this controller
     */
    abstract protected function getModuleType(): string;
}
