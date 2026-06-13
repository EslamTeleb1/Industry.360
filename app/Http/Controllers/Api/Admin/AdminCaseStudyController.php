<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CaseStudyResource;
use App\Models\CaseStudy;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class AdminCaseStudyController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = CaseStudy::orderByDesc('id');

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

        $this->applyDateFilter($query, $request->input('date_filter'));

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

    public function store(Request $request)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('description'))) {
            $request->merge(['description' => json_decode($request->input('description'), true)]);
        }
        if (is_string($request->input('tags'))) {
            $request->merge(['tags' => json_decode($request->input('s'), true)]);
        }

        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'tags' => ['required', 'array'],
            'tags.en' => ['required', 'array'],
            'tags.en.*' => ['required', 'string'],
            'tags.ar' => ['required', 'array'],
            'tags.ar.*' => ['required', 'string'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('case_studies', 'public');
        }

        $caseStudy = CaseStudy::create($data);

        return $this->createdResponse([
            'case_study' => new CaseStudyResource($caseStudy),
        ], 'Case study created successfully');
    }

    public function show(CaseStudy $caseStudy)
    {
        return $this->successResponse([
            'case_study' => new CaseStudyResource($caseStudy),
        ], 'Case study retrieved successfully');
    }

    public function update(Request $request, CaseStudy $caseStudy)
    {
        if (is_string($request->input('title'))) {
            $request->merge(['title' => json_decode($request->input('title'), true)]);
        }
        if (is_string($request->input('description'))) {
            $request->merge(['description' => json_decode($request->input('description'), true)]);
        }
        if (is_string($request->input('tags'))) {
            $request->merge(['tags' => json_decode($request->input('tags'), true)]);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.en' => ['required_with:tags', 'array'],
            'tags.en.*' => ['required', 'string'],
            'tags.ar' => ['required_with:tags', 'array'],
            'tags.ar.*' => ['required', 'string'],
            'img' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('case_studies', 'public');
        }

        $caseStudy->update($data);

        return $this->successResponse([
            'case_study' => new CaseStudyResource($caseStudy->fresh()),
        ], 'Case study updated successfully');
    }

    public function destroy(CaseStudy $caseStudy)
    {
        $caseStudy->delete();

        return $this->successResponse(null, 'Case study deleted successfully');
    }
}
