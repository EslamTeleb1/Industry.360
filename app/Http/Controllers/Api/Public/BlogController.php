<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 10)));
        $categoryId = $request->input('category_id');
        $search = trim((string) $request->input('search', ''));

        $query = Blog::with('category')->where('is_active', true)->orderByDesc('date');

        if ($categoryId) {
            $query->where('blog_category_id', $categoryId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        // Apply date filter on the 'date' column
        $this->applyDateFilter($query, $request->input('date_filter'), 'date');

        $blogs = $query->paginate($perPage);

        return $this->successResponse([
            'blogs' => BlogResource::collection($blogs->getCollection()),
            'pagination' => [
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
                'per_page' => $blogs->perPage(),
                'total' => $blogs->total(),
            ],
        ], 'Blogs retrieved successfully');
    }

    public function show(Blog $blog)
    {
        if (!$blog->is_active) {
            return $this->notFoundResponse('Blog not found');
        }

        $blog->load('category');

        return $this->successResponse([
            'blog' => new BlogResource($blog),
        ], 'Blog retrieved successfully');
    }

    public function categories()
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('title')->get();

        return $this->successResponse([
            'categories' => BlogCategoryResource::collection($categories),
        ], 'Blog categories retrieved successfully');
    }
}
