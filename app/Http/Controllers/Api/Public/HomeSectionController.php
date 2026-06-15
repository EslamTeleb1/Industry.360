<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeSectionResource;
use App\Models\HomeSection;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $homeSettingId = $request->query('home_setting_id');

        $sections = HomeSection::query()
            ->when($homeSettingId, fn($q) => $q->where('home_setting_id', $homeSettingId))
            ->get();

        return $this->successResponse([
            'sections' => HomeSectionResource::collection($sections),
        ], 'Home sections retrieved');
    }

    public function show(HomeSection $section)
    {
        return $this->successResponse([
            'section' => new HomeSectionResource($section),
        ], 'Home section retrieved');
    }
}
