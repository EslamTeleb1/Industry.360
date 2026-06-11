<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\MethodologyResource;
use App\Http\Resources\MethodologySettingResource;
use App\Models\Methodology;
use App\Models\MethodologySetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MethodologyController extends Controller
{
    use ApiResponse;

    public function setting()
    {
        $setting = MethodologySetting::query()->first();

        return $this->successResponse([
            'setting' => $setting ? new MethodologySettingResource($setting) : null,
        ], 'Methodology setting retrieved successfully');
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = Methodology::where('is_active', true)->orderBy('order');

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
            'methodologies' => MethodologyResource::collection($items->getCollection()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Methodologies retrieved successfully');
    }
}
