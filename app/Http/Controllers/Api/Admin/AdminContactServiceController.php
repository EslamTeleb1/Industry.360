<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactLookupResource;
use App\Models\ContactService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminContactServiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = ContactService::orderBy('order')->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate($perPage);

        return $this->successResponse([
            'services' => ContactLookupResource::collection($services->getCollection()),
            'pagination' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
            ],
        ], 'Contact services retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'img_path' => ['nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service = ContactService::create($data);

        return $this->createdResponse([
            'service' => new ContactLookupResource($service),
        ], 'Contact service created successfully');
    }

    public function show(ContactService $service)
    {
        $service->load('packages');

        return $this->successResponse([
            'service' => new ContactLookupResource($service),
        ], 'Contact service retrieved successfully');
    }

    public function update(Request $request, ContactService $service)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
            'img_path' => ['sometimes', 'nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service->update($data);
        $service->load('packages');

        return $this->successResponse([
            'service' => new ContactLookupResource($service),
        ], 'Contact service updated successfully');
    }

    public function destroy(ContactService $service)
    {
        $service->delete();

        return $this->successResponse(null, 'Contact service deleted successfully');
    }
}
