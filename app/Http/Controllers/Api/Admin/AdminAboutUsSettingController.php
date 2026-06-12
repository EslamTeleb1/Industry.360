<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutUsSettingResource;
use App\Models\AboutUsSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminAboutUsSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = AboutUsSetting::query()->first();

        return $this->successResponse([
            'setting' => $setting ? new AboutUsSettingResource($setting) : null,
        ], 'About Us setting retrieved successfully');
    }

    public function update(Request $request)
    {
        $jsonFields = [
            'title', 'description',
            'sub_title', 'sub_description',
            'percentage_title_1', 'percentage_description_1',
            'percentage_title_2', 'percentage_description_2',
            'percentage_title_3', 'percentage_description_3',
        ];
        foreach ($jsonFields as $field) {
            if (is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true)]);
            }
        }

        $data = $request->validate([
            // Main section
            'title' => ['nullable', 'array'],
            'title.en' => ['nullable', 'string'],
            'title.ar' => ['nullable', 'string'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],

            // Sub-about section
            'sub_title' => ['nullable', 'array'],
            'sub_title.en' => ['nullable', 'string'],
            'sub_title.ar' => ['nullable', 'string'],
            'sub_description' => ['nullable', 'array'],
            'sub_description.en' => ['nullable', 'string'],
            'sub_description.ar' => ['nullable', 'string'],

            // Percentage items
            'percentage_title_1' => ['nullable', 'array'],
            'percentage_title_1.en' => ['nullable', 'string'],
            'percentage_title_1.ar' => ['nullable', 'string'],
            'percentage_description_1' => ['nullable', 'array'],
            'percentage_description_1.en' => ['nullable', 'string'],
            'percentage_description_1.ar' => ['nullable', 'string'],
            'percentage_value_1' => ['nullable', 'integer', 'min:0', 'max:100'],

            'percentage_title_2' => ['nullable', 'array'],
            'percentage_title_2.en' => ['nullable', 'string'],
            'percentage_title_2.ar' => ['nullable', 'string'],
            'percentage_description_2' => ['nullable', 'array'],
            'percentage_description_2.en' => ['nullable', 'string'],
            'percentage_description_2.ar' => ['nullable', 'string'],
            'percentage_value_2' => ['nullable', 'integer', 'min:0', 'max:100'],

            'percentage_title_3' => ['nullable', 'array'],
            'percentage_title_3.en' => ['nullable', 'string'],
            'percentage_title_3.ar' => ['nullable', 'string'],
            'percentage_description_3' => ['nullable', 'array'],
            'percentage_description_3.en' => ['nullable', 'string'],
            'percentage_description_3.ar' => ['nullable', 'string'],
            'percentage_value_3' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $setting = AboutUsSetting::query()->firstOrCreate([]);

        // Update all translatable fields individually to preserve existing translations
        $translatableFields = [
            'title', 'description', 'sub_title', 'sub_description',
            'percentage_title_1', 'percentage_description_1',
            'percentage_title_2', 'percentage_description_2',
            'percentage_title_3', 'percentage_description_3',
        ];

        foreach ($translatableFields as $field) {
            if (array_key_exists($field, $data)) {
                $setting->$field = $data[$field];
            }
        }

        // Update percentage values
        foreach (['percentage_value_1', 'percentage_value_2', 'percentage_value_3'] as $field) {
            if (array_key_exists($field, $data)) {
                $setting->$field = $data[$field];
            }
        }

        $setting->save();

        return $this->successResponse([
            'setting' => new AboutUsSettingResource($setting->fresh()),
        ], 'About Us setting updated successfully');
    }
}
