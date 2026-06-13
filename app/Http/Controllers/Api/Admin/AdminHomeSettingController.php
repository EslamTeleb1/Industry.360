<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeSettingResource;
use App\Models\HomeSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminHomeSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = HomeSetting::query()->first();

        return $this->successResponse(
            $setting ? new HomeSettingResource($setting) : null
        , 'Home setting retrieved successfully');
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

        $setting = HomeSetting::query()->firstOrCreate([], [
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
            'setting' => new HomeSettingResource($setting->fresh()),
        ], 'Home setting updated successfully');
    }
}
