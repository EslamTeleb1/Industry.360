<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CaseStudyController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = CaseStudy::where('is_active', true)->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%")
                  ->orWhere('tags->en', 'like', "%{$search}%")
                  ->orWhere('tags->ar', 'like', "%{$search}%");
            });
        }

        $caseStudies = $query->paginate($perPage);

        return $this->successResponse([
            'case_studies' => CaseStudyResource::collection($caseStudies->getCollection()),
            'pagination' => [
                'current_page' => $caseStudies->currentPage(),
                'last_page' => $caseStudies->lastPage(),
                'per_page' => $caseStudies->perPage(),
                'total' => $caseStudies->total(),
            ],
        ], 'Case studies retrieved successfully');
    }

    public function show(CaseStudy $caseStudy)
    {
        if (!$caseStudy->is_active) {
            return $this->notFoundResponse('Case study not found');
        }

        return $this->successResponse([
            'case_study' => new CaseStudyResource($caseStudy),
        ], 'Case study retrieved successfully');
    }
}
