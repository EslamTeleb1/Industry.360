<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerPageSettingResource;
use App\Models\CareersPageSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerPageController extends Controller
{
    use ApiResponse;
    public function show()
    {
        $page = CareersPageSetting::query()->first();

        return $this->successResponse([
            'page' => new CareerPageSettingResource($page),
        ], 'Career page retrieved successfully');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'banner' => ['nullable', 'image', 'max:5120'],
        ]);

        $page = CareersPageSetting::query()->firstOrCreate([]);

        if ($request->hasFile('banner')) {
            if ($page->banner_image_path) {
                Storage::disk('public')->delete($page->banner_image_path);
            }
            $page->banner_image_path = $request->file('banner')->store('careers/banner', 'public');
        }

        if (array_key_exists('description', $data)) {
            $page->description = $data['description'];
        }

        $page->save();

        return $this->successResponse([
            'page' => new CareerPageSettingResource($page->fresh()),
        ], 'Career page updated successfully');
    }
}
