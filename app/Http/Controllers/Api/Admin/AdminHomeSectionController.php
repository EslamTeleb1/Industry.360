<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeSectionResource;
use App\Models\HomeSection;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminHomeSectionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $sections = HomeSection::query()->with('homeSetting')->get();

        return $this->successResponse([
            'sections' => HomeSectionResource::collection($sections),
        ], 'Home sections retrieved successfully');
    }

    public function store(Request $request)
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
            'home_setting_id' => ['nullable', 'integer', 'exists:home_settings,id'],

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

            'sub_title' => ['nullable', 'array'],
            'sub_title.en' => ['nullable', 'array', 'size:3'],
            'sub_title.en.*.text' => ['required_with:sub_title.en', 'string'],
            'sub_title.en.*.style' => ['required_with:sub_title.en', 'string'],
            'sub_title.ar' => ['nullable', 'array', 'size:3'],
            'sub_title.ar.*.text' => ['required_with:sub_title.ar', 'string'],
            'sub_title.ar.*.style' => ['required_with:sub_title.ar', 'string'],
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

        $section = new HomeSection();
        $section->home_setting_id = $data['home_setting_id'] ?? null;

        if (array_key_exists('title', $data)) {
            $section->sub_title = $data['title'];
        } elseif (array_key_exists('sub_title', $data)) {
            $section->sub_title = $data['sub_title'];
        }

        if (array_key_exists('description', $data)) {
            $section->sub_description = $data['description'];
        } elseif (array_key_exists('sub_description', $data)) {
            $section->sub_description = $data['sub_description'];
        }

        foreach (['percentage_title_1', 'percentage_description_1', 'percentage_title_2', 'percentage_description_2', 'percentage_title_3', 'percentage_description_3'] as $tfield) {
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
            $path = $request->file('img')->store('home_sections', 'public');
            $section->img = $path;
        }

        $section->save();

        return $this->createdResponse([
            'section' => new HomeSectionResource($section),
        ], 'Home section created successfully');
    }

    public function show(HomeSection $section)
    {
        return $this->successResponse([
            'section' => new HomeSectionResource($section->load('homeSetting')),
        ], 'Home section retrieved successfully');
    }

    public function update(Request $request, HomeSection $section)
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
            'home_setting_id' => ['nullable', 'integer', 'exists:home_settings,id'],

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

            'sub_title' => ['nullable', 'array'],
            'sub_title.en' => ['nullable', 'array', 'size:3'],
            'sub_title.en.*.text' => ['required_with:sub_title.en', 'string'],
            'sub_title.en.*.style' => ['required_with:sub_title.en', 'string'],
            'sub_title.ar' => ['nullable', 'array', 'size:3'],
            'sub_title.ar.*.text' => ['required_with:sub_title.ar', 'string'],
            'sub_title.ar.*.style' => ['required_with:sub_title.ar', 'string'],
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

        if (array_key_exists('home_setting_id', $data)) {
            $section->home_setting_id = $data['home_setting_id'];
        }

        if (array_key_exists('title', $data)) {
            $section->sub_title = $data['title'];
        } elseif (array_key_exists('sub_title', $data)) {
            $section->sub_title = $data['sub_title'];
        }

        if (array_key_exists('description', $data)) {
            $section->sub_description = $data['description'];
        } elseif (array_key_exists('sub_description', $data)) {
            $section->sub_description = $data['sub_description'];
        }

        foreach (['percentage_title_1', 'percentage_description_1', 'percentage_title_2', 'percentage_description_2', 'percentage_title_3', 'percentage_description_3'] as $tfield) {
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
            $path = $request->file('img')->store('home_sections', 'public');
            $section->img = $path;
        } elseif (array_key_exists('img', $data)) {
            $section->img = $data['img'];
        }

        $section->save();

        return $this->successResponse([
            'section' => new HomeSectionResource($section),
        ], 'Home section updated successfully');
    }

    public function destroy(HomeSection $section)
    {
        if ($section->img) {
            Storage::disk('public')->delete($section->img);
        }
        $section->delete();

        return $this->successResponse(null, 'Home section deleted successfully');
    }
}
