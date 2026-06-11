<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Http\Resources\TeamSettingResource;
use App\Models\TeamMember;
use App\Models\TeamSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use ApiResponse;

    public function setting()
    {
        $setting = TeamSetting::query()->first();

        return $this->successResponse([
            'setting' => $setting ? new TeamSettingResource($setting) : null,
        ], 'Team setting retrieved successfully');
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = TeamMember::where('is_active', true)->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('position->en', 'like', "%{$search}%")
                  ->orWhere('position->ar', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate($perPage);

        return $this->successResponse([
            'team_members' => TeamMemberResource::collection($items->getCollection()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Team members retrieved successfully');
    }
}
