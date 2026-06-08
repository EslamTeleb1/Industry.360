<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminFaqCategoryController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = FaqCategory::orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'like', "%{$search}%")
                  ->orWhere('name->ar', 'like', "%{$search}%");
            });
        }

        $this->applyDateFilter($query, $request->input('date_filter'));

        $categories = $query->paginate($perPage);

        return $this->successResponse([
            'categories' => FaqCategoryResource::collection($categories->getCollection()),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ], 'FAQ categories retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category = FaqCategory::create($data);

        return $this->createdResponse([
            'category' => new FaqCategoryResource($category),
        ], 'FAQ category created successfully');
    }

    public function show(FaqCategory $faqCategory)
    {
        return $this->successResponse([
            'category' => new FaqCategoryResource($faqCategory),
        ], 'FAQ category retrieved successfully');
    }

    public function update(Request $request, FaqCategory $faqCategory)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'array'],
            'name.en' => ['required_with:name', 'string'],
            'name.ar' => ['required_with:name', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $faqCategory->update($data);

        return $this->successResponse([
            'category' => new FaqCategoryResource($faqCategory->fresh()),
        ], 'FAQ category updated successfully');
    }

    public function destroy(FaqCategory $faqCategory)
    {
        $faqCategory->delete();

        return $this->successResponse(null, 'FAQ category deleted successfully');
    }
}
