<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCategoryResource;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $categoryId = $request->input('category_id');
        $search = trim((string) $request->input('search', ''));

        $query = Faq::with('category')->where('is_active', true)->orderBy('order');

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

    public function categories()
    {
        $categories = FaqCategory::where('is_active', true)->get();

        return $this->successResponse([
            'categories' => FaqCategoryResource::collection($categories),
        ], 'FAQ categories retrieved successfully');
    }
}
