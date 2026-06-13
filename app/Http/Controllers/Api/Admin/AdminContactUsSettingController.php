<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactUsSettingResource;
use App\Models\ContactUsSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminContactUsSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = ContactUsSetting::query()->first();

        return $this->successResponse(
            $setting ? new ContactUsSettingResource($setting) : null
        , 'Contact us setting retrieved successfully');
    }

    public function update(Request $request)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('description'))) {
            $request->merge(['description' => json_decode($request->input('description'), true)]);
        }

        $data = $request->validate([
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
        ]);

        $setting = ContactUsSetting::query()->firstOrCreate([], [
            'title' => ['en' => '', 'ar' => ''],
            'description' => ['en' => '', 'ar' => ''],
        ]);

        if (array_key_exists('title', $data)) {
            $setting->title = $data['title'];
        }
        if (array_key_exists('description', $data)) {
            $setting->description = $data['description'];
        }

        $setting->save();

        return $this->successResponse([
            'setting' => new ContactUsSettingResource($setting->fresh()),
        ], 'Contact us setting updated successfully');
    }
}
