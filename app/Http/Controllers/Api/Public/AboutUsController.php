<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutUsSettingResource;
use App\Models\AboutUsSetting;
use App\Traits\ApiResponse;

class AboutUsSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = AboutUsSetting::query()->first();

        return $this->successResponse(
            $setting ? new AboutUsSettingResource($setting) : null
        , 'About Us setting retrieved successfully');
    }
}
