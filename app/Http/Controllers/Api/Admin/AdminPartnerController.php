<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminPartnerController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = Partner::orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('subtitle->en', 'like', "%{$search}%")
                  ->orWhere('subtitle->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $this->applyDateFilter($query, $request->input('date_filter'));

        $partners = $query->paginate($perPage);

        return $this->successResponse([
            'partners' => PartnerResource::collection($partners->getCollection()),
            'pagination' => [
                'current_page' => $partners->currentPage(),
                'last_page' => $partners->lastPage(),
                'per_page' => $partners->perPage(),
                'total' => $partners->total(),
            ],
        ], 'Partners retrieved successfully');
    }

    public function store(Request $request)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('subtitle'))) {
            $request->merge(['subtitle' => json_decode($request->input('subtitle'), true)]);
        }
        if (is_string($request->input('description'))) {
            $request->merge(['description' => json_decode($request->input('description'), true)]);
        }

        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'subtitle' => ['required', 'array'],
            'subtitle.en' => ['required', 'string'],
            'subtitle.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('partners', 'public');
        }

        $partner = Partner::create($data);

        return $this->createdResponse([
            'partner' => new PartnerResource($partner),
        ], 'Partner created successfully');
    }

    public function show(Partner $partner)
    {
        return $this->successResponse([
            'partner' => new PartnerResource($partner),
        ], 'Partner retrieved successfully');
    }

    public function update(Request $request, Partner $partner)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('subtitle'))) {
            $request->merge(['subtitle' => json_decode($request->input('subtitle'), true)]);
        }
        if (is_string($request->input('description'))) {
            $request->merge(['description' => json_decode($request->input('description'), true)]);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'subtitle' => ['sometimes', 'array'],
            'subtitle.en' => ['required_with:subtitle', 'string'],
            'subtitle.ar' => ['required_with:subtitle', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('partners', 'public');
        }

        $partner->update($data);

        return $this->successResponse([
            'partner' => new PartnerResource($partner->fresh()),
        ], 'Partner updated successfully');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return $this->successResponse(null, 'Partner deleted successfully');
    }
}
