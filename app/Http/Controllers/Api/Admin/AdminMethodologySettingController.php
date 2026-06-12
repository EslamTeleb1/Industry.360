<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MethodologySettingResource;
use App\Models\MethodologySetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminMethodologySettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = MethodologySetting::query()->first();

        return $this->successResponse([
           $setting ? new MethodologySettingResource($setting) : null,
        ], 'Methodology setting retrieved successfully');
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

        $setting = MethodologySetting::query()->firstOrCreate([], [
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
            'setting' => new MethodologySettingResource($setting->fresh()),
        ], 'Methodology setting updated successfully');
    }
}
