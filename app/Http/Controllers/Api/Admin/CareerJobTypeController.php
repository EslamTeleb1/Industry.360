<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerJobTypeResource;
use App\Models\CareerJobType;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class CareerJobTypeController extends Controller
{
    use ApiResponse, DateFilterable;
    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 15)));
        $sortDirection = strtolower($request->string('sort', 'asc')->toString()) === 'desc' ? 'desc' : 'asc';
        $search = trim((string) $request->input('search', ''));

        $jobTypes = CareerJobType::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name->en', 'like', "%{$search}%")
                        ->orWhere('name->ar', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', $sortDirection);

        // Date filter
        $this->applyDateFilter($jobTypes, $request->input('date_filter'));

        $jobTypes = $jobTypes->paginate($perPage);

        return $this->successResponse([
            'job_types' => CareerJobTypeResource::collection($jobTypes->getCollection()),
            'pagination' => [
                'url' => $jobTypes->url($jobTypes->currentPage()),
                'current_page' => $jobTypes->currentPage(),
                'last_page' => $jobTypes->lastPage(),
                'per_page' => $jobTypes->perPage(),
                'total' => $jobTypes->total(),
                'from' => $jobTypes->firstItem(),
                'to' => $jobTypes->lastItem(),
            ],
        ], 'Job types retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
        ]);

        $jobType = CareerJobType::create($data);

        return $this->createdResponse([
            'job_type' => new CareerJobTypeResource($jobType),
        ], 'Job type created successfully');
    }

    public function update(Request $request, CareerJobType $jobType)
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
        ]);

        $jobType->update($data);

        return $this->successResponse([
            'job_type' => new CareerJobTypeResource($jobType->fresh()),
        ], 'Job type updated successfully');
    }

    public function destroy(CareerJobType $jobType)
    {
        $jobType->delete();

        return $this->successResponse(null, 'Job type deleted successfully');
    }
}
