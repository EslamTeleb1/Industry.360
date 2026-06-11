<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminTeamMemberController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = TeamMember::orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('position->en', 'like', "%{$search}%")
                  ->orWhere('position->ar', 'like', "%{$search}%");
            });
        }

        $this->applyDateFilter($query, $request->input('date_filter'));

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

    public function store(Request $request)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('position'))) {
            $request->merge(['position' => json_decode($request->input('position'), true)]);
        }

        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'position' => ['required', 'array'],
            'position.en' => ['required', 'string'],
            'position.ar' => ['required', 'string'],
            'link' => ['nullable', 'string', 'url'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('team_members', 'public');
        }

        $item = TeamMember::create($data);

        return $this->createdResponse([
            'team_member' => new TeamMemberResource($item),
        ], 'Team member created successfully');
    }

    public function show(TeamMember $teamMember)
    {
        return $this->successResponse([
            'team_member' => new TeamMemberResource($teamMember),
        ], 'Team member retrieved successfully');
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('position'))) {
            $request->merge(['position' => json_decode($request->input('position'), true)]);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'position' => ['sometimes', 'array'],
            'position.en' => ['required_with:position', 'string'],
            'position.ar' => ['required_with:position', 'string'],
            'link' => ['nullable', 'string', 'url'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('team_members', 'public');
        }

        $teamMember->update($data);

        return $this->successResponse([
            'team_member' => new TeamMemberResource($teamMember->fresh()),
        ], 'Team member updated successfully');
    }

    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();

        return $this->successResponse(null, 'Team member deleted successfully');
    }
}
