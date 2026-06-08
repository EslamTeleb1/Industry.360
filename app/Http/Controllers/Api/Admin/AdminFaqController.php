<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $categoryId = $request->input('category_id');
        $search = trim((string) $request->input('search', ''));

        $query = Faq::with('category')->orderBy('order');

        if ($categoryId) {
            $query->where('faq_category_id', $categoryId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('answer->en', 'like', "%{$search}%")
                  ->orWhere('answer->ar', 'like', "%{$search}%");
            });
        }

        $this->applyDateFilter($query, $request->input('date_filter'));

        $faqs = $query->paginate($perPage);

        return $this->successResponse([
            'faqs' => FaqResource::collection($faqs->getCollection()),
            'pagination' => [
                'current_page' => $faqs->currentPage(),
                'last_page' => $faqs->lastPage(),
                'per_page' => $faqs->perPage(),
                'total' => $faqs->total(),
            ],
        ], 'FAQs retrieved successfully');
    }

    public function store(Request $request)
    {
        if (is_string($request->input('answer'))) {
            $request->merge(['answer' => json_decode($request->input('answer'), true)]);
        }

        $data = $request->validate([
            'faq_category_id' => ['required', 'integer', 'exists:faq_categories,id'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'answer' => ['required', 'array'],
            'answer.en' => ['required', 'string'],
            'answer.ar' => ['required', 'string'],
            'order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $faq = Faq::create($data);
        $faq->load('category');

        return $this->createdResponse([
            'faq' => new FaqResource($faq),
        ], 'FAQ created successfully');
    }

    public function show(Faq $faq)
    {
        $faq->load('category');

        return $this->successResponse([
            'faq' => new FaqResource($faq),
        ], 'FAQ retrieved successfully');
    }

    public function update(Request $request, Faq $faq)
    {
        if (is_string($request->input('answer'))) {
            $request->merge(['answer' => json_decode($request->input('answer'), true)]);
        }

        $data = $request->validate([
            'faq_category_id' => ['sometimes', 'integer', 'exists:faq_categories,id'],
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'answer' => ['sometimes', 'array'],
            'answer.en' => ['required_with:answer', 'string'],
            'answer.ar' => ['required_with:answer', 'string'],
            'order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $faq->update($data);
        $faq->load('category');

        return $this->successResponse([
            'faq' => new FaqResource($faq->fresh()),
        ], 'FAQ updated successfully');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return $this->successResponse(null, 'FAQ deleted successfully');
    }
}
