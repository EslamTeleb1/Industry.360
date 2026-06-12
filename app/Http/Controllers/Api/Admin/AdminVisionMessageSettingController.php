<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisionMessageSettingResource;
use App\Models\VisionMessageSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminVisionMessageSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = VisionMessageSetting::query()->first();

        return $this->successResponse([
            'setting' => $setting ? new VisionMessageSettingResource($setting) : null,
        ], 'Vision & Message setting retrieved successfully');
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
            'title.en' => ['nullable', 'string'],
            'title.ar' => ['nullable', 'string'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
        ]);

        $setting = VisionMessageSetting::query()->firstOrCreate([], [
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
            'setting' => new VisionMessageSettingResource($setting->fresh()),
        ], 'Vision & Message setting updated successfully');
    }
}
