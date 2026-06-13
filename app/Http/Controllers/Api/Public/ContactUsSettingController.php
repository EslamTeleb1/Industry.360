<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactUsSettingResource;
use App\Models\ContactUsSetting;
use App\Traits\ApiResponse;

class ContactUsSettingController extends Controller
{
    use ApiResponse;

    public function show()
    {
        $setting = ContactUsSetting::query()->first();

        return $this->successResponse(
            $setting ? new ContactUsSettingResource($setting) : null
        , 'Contact us setting retrieved successfully');
    }
}
