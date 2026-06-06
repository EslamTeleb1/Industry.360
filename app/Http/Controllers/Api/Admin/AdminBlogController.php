<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminBlogController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = Blog::with('category')->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $this->applyDateFilter($query, $request->input('date_filter'));

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'blog_category_id' => ['required', 'integer', 'exists:blog_categories,id'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'date' => ['required', 'date'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('blogs', 'public');
        }

        $blog = Blog::create($data);
        $blog->load('category');

        return $this->createdResponse([
            'blog' => new BlogResource($blog),
        ], 'Blog created successfully');
    }

    public function show(Blog $blog)
    {
        $blog->load('category');

        return $this->successResponse([
            'blog' => new BlogResource($blog),
        ], 'Blog retrieved successfully');
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'blog_category_id' => ['sometimes', 'integer', 'exists:blog_categories,id'],
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
            'date' => ['sometimes', 'date'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('blogs', 'public');
        }

        $blog->update($data);
        $blog->load('category');

        return $this->successResponse([
            'blog' => new BlogResource($blog),
        ], 'Blog updated successfully');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();

        return $this->successResponse(null, 'Blog deleted successfully');
    }
}
