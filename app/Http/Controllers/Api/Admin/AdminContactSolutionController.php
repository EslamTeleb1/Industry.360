<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactLookupResource;
use App\Models\ContactSolution;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminContactSolutionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = ContactSolution::orderBy('order')->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $solutions = $query->paginate($perPage);

        return $this->successResponse([
            'solutions' => ContactLookupResource::collection($solutions->getCollection()),
            'service_type' => 'contact_solution', // hardcoded since it's the only type
            'pagination' => [
                'current_page' => $solutions->currentPage(),
                'last_page' => $solutions->lastPage(),
                'per_page' => $solutions->perPage(),
                'total' => $solutions->total(),
            ],
        ], 'Contact solutions retrieved successfully');
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

        $solution = ContactSolution::create($data);

        return $this->createdResponse([
            'solution' => new ContactLookupResource($solution),
        ], 'Contact solution created successfully');
    }

    public function show(ContactSolution $solution)
    {
        $solution->load('packages');

        return $this->successResponse([
            'solution' => new ContactLookupResource($solution),
        ], 'Contact solution retrieved successfully');
    }

    public function update(Request $request, ContactSolution $solution)
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

        $solution->update($data);
        $solution->load('packages');

        return $this->successResponse([
            'solution' => new ContactLookupResource($solution),
        ], 'Contact solution updated successfully');
    }

    public function destroy(ContactSolution $solution)
    {
        $solution->delete();

        return $this->successResponse(null, 'Contact solution deleted successfully');
    }
}
