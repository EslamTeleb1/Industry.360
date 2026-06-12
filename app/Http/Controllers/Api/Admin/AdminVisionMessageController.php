<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisionMessageResource;
use App\Models\VisionMessage;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminVisionMessageController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = VisionMessage::orderByDesc('id');

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
            'vision_messages' => VisionMessageResource::collection($items->getCollection()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], 'Vision messages retrieved successfully');
    }

    public function store(Request $request)
    {
        $jsonFields = ['title', 'description'];
        foreach ($jsonFields as $field) {
            if (is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true)]);
            }
        }

        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
        ]);

        $item = VisionMessage::create($data);

        return $this->createdResponse([
            'vision_message' => new VisionMessageResource($item),
        ], 'Vision message created successfully');
    }

    public function show(VisionMessage $visionMessage)
    {
        return $this->successResponse([
            'vision_message' => new VisionMessageResource($visionMessage),
        ], 'Vision message retrieved successfully');
    }

    public function update(Request $request, VisionMessage $visionMessage)
    {
        $jsonFields = ['title', 'description'];
        foreach ($jsonFields as $field) {
            if (is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true)]);
            }
        }

        $data = $request->validate([
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
        ]);

        $visionMessage->update($data);

        return $this->successResponse([
            'vision_message' => new VisionMessageResource($visionMessage->fresh()),
        ], 'Vision message updated successfully');
    }

    public function destroy(VisionMessage $visionMessage)
    {
        $visionMessage->delete();

        return $this->successResponse(null, 'Vision message deleted successfully');
    }
}
