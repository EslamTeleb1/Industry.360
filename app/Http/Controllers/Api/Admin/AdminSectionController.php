<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSectionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $sections = Section::query()->get();

        return $this->successResponse([
            'sections' => SectionResource::collection($sections),
        ], 'Sections retrieved successfully');
    }

    public function store(Request $request)
    {
        $jsonFields = [
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
            'sub_title' => ['nullable', 'array'],
            'sub_title.en' => ['nullable', 'string'],
            'sub_title.ar' => ['nullable', 'string'],
            'sub_description' => ['nullable', 'array'],
            'sub_description.en' => ['nullable', 'string'],
            'sub_description.ar' => ['nullable', 'string'],

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

            'img' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $section = new Section();

        foreach (['sub_title', 'sub_description', 'percentage_title_1', 'percentage_description_1', 'percentage_title_2', 'percentage_description_2', 'percentage_title_3', 'percentage_description_3'] as $tfield) {
            if (array_key_exists($tfield, $data)) {
                $section->$tfield = $data[$tfield];
            }
        }

        foreach (['percentage_value_1', 'percentage_value_2', 'percentage_value_3'] as $numField) {
            if (array_key_exists($numField, $data)) {
                $section->$numField = $data[$numField];
            }
        }

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('sections', 'public');
            $section->img = $path;
        }

        $section->save();

        return $this->createdResponse([
            'section' => new SectionResource($section),
        ], 'Section created successfully');
    }

    public function show(Section $section)
    {
        return $this->successResponse([
            'section' => new SectionResource($section),
        ], 'Section retrieved successfully');
    }

    public function update(Request $request, Section $section)
    {
        $jsonFields = [
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
            'sub_title' => ['nullable', 'array'],
            'sub_title.en' => ['nullable', 'string'],
            'sub_title.ar' => ['nullable', 'string'],
            'sub_description' => ['nullable', 'array'],
            'sub_description.en' => ['nullable', 'string'],
            'sub_description.ar' => ['nullable', 'string'],

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

            'img' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        foreach (['sub_title', 'sub_description', 'percentage_title_1', 'percentage_description_1', 'percentage_title_2', 'percentage_description_2', 'percentage_title_3', 'percentage_description_3'] as $tfield) {
            if (array_key_exists($tfield, $data)) {
                $section->$tfield = $data[$tfield];
            }
        }

        foreach (['percentage_value_1', 'percentage_value_2', 'percentage_value_3'] as $numField) {
            if (array_key_exists($numField, $data)) {
                $section->$numField = $data[$numField];
            }
        }

        if ($request->hasFile('img')) {
            if ($section->img) {
                Storage::disk('public')->delete($section->img);
            }
            $path = $request->file('img')->store('sections', 'public');
            $section->img = $path;
        } elseif (array_key_exists('img', $data)) {
            $section->img = $data['img'];
        }

        $section->save();

        return $this->successResponse([
            'section' => new SectionResource($section),
        ], 'Section updated successfully');
    }

    public function destroy(Section $section)
    {
        if ($section->img) {
            Storage::disk('public')->delete($section->img);
        }
        $section->delete();

        return $this->successResponse(null, 'Section deleted successfully');
    }
}
