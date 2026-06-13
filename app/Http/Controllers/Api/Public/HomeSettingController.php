<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeSettingResource;
use App\Models\HomeSetting;
use App\Traits\ApiResponse;

class HomeSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = HomeSetting::query()->first();

        return $this->successResponse([
            'setting' => $setting ? new HomeSettingResource($setting) : null,
        ], 'Home setting retrieved successfully');
    }
}
