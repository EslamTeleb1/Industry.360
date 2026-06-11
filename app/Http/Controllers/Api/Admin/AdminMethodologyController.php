<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MethodologyResource;
use App\Models\Methodology;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminMethodologyController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = Methodology::orderBy('order');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $this->applyDateFilter($query, $request->input('date_filter'));

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

    public function store(Request $request)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('description'))) {
            $request->merge(['description' => json_decode($request->input('description'), true)]);
        }

        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $item = Methodology::create($data);

        return $this->createdResponse([
            'methodology' => new MethodologyResource($item),
        ], 'Methodology created successfully');
    }

    public function show(Methodology $methodology)
    {
        return $this->successResponse([
            'methodology' => new MethodologyResource($methodology),
        ], 'Methodology retrieved successfully');
    }

    public function update(Request $request, Methodology $methodology)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('description'))) {
            $request->merge(['description' => json_decode($request->input('description'), true)]);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $methodology->update($data);

        return $this->successResponse([
            'methodology' => new MethodologyResource($methodology->fresh()),
        ], 'Methodology updated successfully');
    }

    public function destroy(Methodology $methodology)
    {
        $methodology->delete();

        return $this->successResponse(null, 'Methodology deleted successfully');
    }
}
