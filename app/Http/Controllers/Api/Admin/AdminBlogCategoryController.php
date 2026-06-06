<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Models\BlogCategory;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminBlogCategoryController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));
        
        $query = BlogCategory::orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%");
            });
        }

        $this->applyDateFilter($query, $request->input('date_filter'));

        $categories = $query->paginate($perPage);

        return $this->successResponse([
            'categories' => BlogCategoryResource::collection($categories->getCollection()),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ], 'Blog categories retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category = BlogCategory::create($data);

        return $this->createdResponse([
            'category' => new BlogCategoryResource($category),
        ], 'Blog category created successfully');
    }

    public function show(BlogCategory $blogCategory)
    {
        return $this->successResponse([
            'category' => new BlogCategoryResource($blogCategory),
        ], 'Blog category retrieved successfully');
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $blogCategory->update($data);

        return $this->successResponse([
            'category' => new BlogCategoryResource($blogCategory->fresh()),
        ], 'Blog category updated successfully');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();

        return $this->successResponse(null, 'Blog category deleted successfully');
    }
}
