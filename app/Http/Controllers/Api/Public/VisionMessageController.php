<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisionMessageResource;
use App\Http\Resources\VisionMessageSettingResource;
use App\Models\VisionMessage;
use App\Models\VisionMessageSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class VisionMessageController extends Controller
{
    use ApiResponse;

    public function setting()
    {
        $setting = VisionMessageSetting::query()->first();

        return $this->successResponse([
            'setting' => $setting ? new VisionMessageSettingResource($setting) : null,
        ], 'Vision & Message setting retrieved successfully');
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = VisionMessage::where('is_active', true)->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate($perPage);

        return $this->successResponse([
            'vision_messages' => VisionMessageResource::collection($items->getCollection()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Vision messages retrieved successfully');
    }
}
