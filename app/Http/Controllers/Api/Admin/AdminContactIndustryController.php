<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactLookupResource;
use App\Models\ContactIndustry;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminContactIndustryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = ContactIndustry::orderBy('order')->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $industries = $query->paginate($perPage);

        return $this->successResponse([
            'industries' => ContactLookupResource::collection($industries->getCollection()),
            'pagination' => [
                'current_page' => $industries->currentPage(),
                'last_page' => $industries->lastPage(),
                'per_page' => $industries->perPage(),
                'total' => $industries->total(),
            ],
        ], 'Contact industries retrieved successfully');
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

        $industry = ContactIndustry::create($data);

        return $this->createdResponse([
            'industry' => new ContactLookupResource($industry),
        ], 'Contact industry created successfully');
    }

    public function show(ContactIndustry $industry)
    {
        $industry->load('packages');

        return $this->successResponse([
            'industry' => new ContactLookupResource($industry),
        ], 'Contact industry retrieved successfully');
    }

    public function update(Request $request, ContactIndustry $industry)
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

        $industry->update($data);
        $industry->load('packages');

        return $this->successResponse([
            'industry' => new ContactLookupResource($industry),
        ], 'Contact industry updated successfully');
    }

    public function destroy(ContactIndustry $industry)
    {
        $industry->delete();

        return $this->successResponse(null, 'Contact industry deleted successfully');
    }
}
