<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeSettingResource;
use App\Models\HomeSetting;
use App\Models\HomeSection;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminHomeSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = HomeSetting::query()->with('sections')->first();

        return $this->successResponse([
            'setting' => $setting ? new HomeSettingResource($setting) : null,
        ], 'Home setting retrieved successfully');
    }

    public function update(Request $request)
    {
        // If sections are provided as JSON string, decode it
        if (is_string($request->input('sections'))) {
            $request->merge(['sections' => json_decode($request->input('sections'), true)]);
        }

        $jsonFields = [
            'title', 'description',
            'sub_main_title', 'sub_main_description',
        ];
        foreach ($jsonFields as $field) {
            if (is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true)]);
            }
        }

        $data = $request->validate([
            // Main section
            'title' => ['nullable', 'array'],
            'title.en' => ['nullable', 'array', 'size:3'],
            'title.en.*.text' => ['required_with:title.en', 'string'],
            'title.en.*.style' => ['required_with:title.en', 'string'],
            'title.ar' => ['nullable', 'array', 'size:3'],
            'title.ar.*.text' => ['required_with:title.ar', 'string'],
            'title.ar.*.style' => ['required_with:title.ar', 'string'],
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

            // Sub-main section
            'sub_main_title' => ['nullable', 'array'],
            'sub_main_title.en' => ['nullable', 'string'],
            'sub_main_title.ar' => ['nullable', 'string'],
            'sub_main_description' => ['nullable', 'array'],
            'sub_main_description.en' => ['nullable', 'string'],
            'sub_main_description.ar' => ['nullable', 'string'],

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

            // Sections
            'sections' => ['nullable', 'array'],
            'sections.*.id' => ['nullable', 'integer', 'exists:home_sections,id'],
            'sections.*.sub_title' => ['nullable', 'array'],
            'sections.*.sub_title.en' => ['nullable', 'string'],
            'sections.*.sub_title.ar' => ['nullable', 'string'],
            'sections.*.sub_description' => ['nullable', 'array'],
            'sections.*.sub_description.en' => ['nullable', 'string'],
            'sections.*.sub_description.ar' => ['nullable', 'string'],
            'sections.*.percentage_title_1' => ['nullable', 'array'],
            'sections.*.percentage_title_1.en' => ['nullable', 'string'],
            'sections.*.percentage_title_1.ar' => ['nullable', 'string'],
            'sections.*.percentage_description_1' => ['nullable', 'array'],
            'sections.*.percentage_description_1.en' => ['nullable', 'string'],
            'sections.*.percentage_description_1.ar' => ['nullable', 'string'],
            'sections.*.percentage_value_1' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sections.*.percentage_title_2' => ['nullable', 'array'],
            'sections.*.percentage_title_2.en' => ['nullable', 'string'],
            'sections.*.percentage_title_2.ar' => ['nullable', 'string'],
            'sections.*.percentage_description_2' => ['nullable', 'array'],
            'sections.*.percentage_description_2.en' => ['nullable', 'string'],
            'sections.*.percentage_description_2.ar' => ['nullable', 'string'],
            'sections.*.percentage_value_2' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sections.*.percentage_title_3' => ['nullable', 'array'],
            'sections.*.percentage_title_3.en' => ['nullable', 'string'],
            'sections.*.percentage_title_3.ar' => ['nullable', 'string'],
            'sections.*.percentage_description_3' => ['nullable', 'array'],
            'sections.*.percentage_description_3.en' => ['nullable', 'string'],
            'sections.*.percentage_description_3.ar' => ['nullable', 'string'],
            'sections.*.percentage_value_3' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sections.*.img' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $setting = HomeSetting::query()->firstOrCreate([]);

        // Update top-level translatable fields
        $translatableFields = [
            'title', 'description',
            'sub_main_title', 'sub_main_description',
        ];

        foreach ($translatableFields as $field) {
            if (array_key_exists($field, $data)) {
                $setting->$field = $data[$field];
            }
        }

        $setting->save();

        // Sync sections: create/update and delete removed ones
        $existingIds = $setting->sections()->pluck('id')->toArray();
        $incomingIds = [];

        if (!empty($data['sections']) && is_array($data['sections'])) {
            foreach ($data['sections'] as $index => $sectionData) {
                $section = null;
                if (!empty($sectionData['id'])) {
                    $section = $setting->sections()->where('id', $sectionData['id'])->first();
                }
                if (!$section) {
                    $section = new HomeSection();
                    $section->home_setting_id = $setting->id;
                }

                // Assign translatable fields
                foreach (['sub_title', 'sub_description', 'percentage_title_1', 'percentage_description_1', 'percentage_title_2', 'percentage_description_2', 'percentage_title_3', 'percentage_description_3'] as $tfield) {
                    if (array_key_exists($tfield, $sectionData)) {
                        $section->$tfield = $sectionData[$tfield];
                    }
                }

                // Assign numeric percentage values
                foreach (['percentage_value_1', 'percentage_value_2', 'percentage_value_3'] as $numField) {
                    if (array_key_exists($numField, $sectionData)) {
                        $section->$numField = $sectionData[$numField];
                    }
                }

                // Handle nested image upload: sections.{index}.img
                if ($request->hasFile("sections.$index.img")) {
                    $file = $request->file("sections.$index.img");
                    $path = $file->store('home_sections', 'public');
                    $section->img = $path;
                } elseif (!empty($sectionData['img']) && is_string($sectionData['img'])) {
                    $section->img = $sectionData['img'];
                }

                $section->save();
                $incomingIds[] = $section->id;
            }
        }

        // Delete removed sections
        $toDelete = array_diff($existingIds, $incomingIds);
        if (!empty($toDelete)) {
            $setting->sections()->whereIn('id', $toDelete)->delete();
        }

        return $this->successResponse([
            'setting' => new HomeSettingResource($setting->fresh()->load('sections')),
        ], 'Home setting updated successfully');
    }
}
