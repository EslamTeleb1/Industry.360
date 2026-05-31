<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerDepartmentResource;
use App\Http\Resources\CareerJobResource;
use App\Http\Resources\CareerJobTypeResource;
use App\Http\Resources\CareerLocationResource;
use App\Http\Resources\CareerPageSettingResource;
use App\Models\CareerDepartment;
use App\Models\CareerJob;
use App\Models\CareerJobType;
use App\Models\CareerLocation;
use App\Models\CareersPageSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CareerPublicController extends Controller
{
    use ApiResponse;
    public function page()
    {
        return $this->successResponse([
            'page' => new CareerPageSettingResource(CareersPageSetting::query()->first()),
        ], 'Career page retrieved successfully');
    }

    public function lookups()
    {
        return $this->successResponse([
            'departments' => CareerDepartmentResource::collection(CareerDepartment::query()->orderBy('id')->get()),
            'locations' => CareerLocationResource::collection(CareerLocation::query()->orderBy('id')->get()),
            'job_types' => CareerJobTypeResource::collection(CareerJobType::query()->orderBy('id')->get()),
        ], 'Lookups retrieved successfully');
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 12)));
        $sortDirection = strtolower($request->string('sort', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->input('search', ''));

        $jobs = CareerJob::query()
            ->with(['department', 'location', 'jobType', 'roleSections'])
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('title->en', 'like', "%{$search}%")
                        ->orWhere('title->ar', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('name->en', 'like', "%{$search}%")
                                ->orWhere('name->ar', 'like', "%{$search}%");
                        })
                        ->orWhereHas('jobType', function ($jobTypeQuery) use ($search) {
                            $jobTypeQuery->where('name->en', 'like', "%{$search}%")
                                ->orWhere('name->ar', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->integer('department_id')))
            ->when($request->filled('location_id'), fn ($q) => $q->where('location_id', $request->integer('location_id')))
            ->when($request->filled('job_type_id'), fn ($q) => $q->where('job_type_id', $request->integer('job_type_id')))
            ->orderBy('created_at', $sortDirection)
            ->paginate($perPage);

        return $this->successResponse([
            'jobs' => CareerJobResource::collection($jobs->getCollection()),
            'pagination' => [
                'url' => $jobs->url($jobs->currentPage()),
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'from' => $jobs->firstItem(),
                'to' => $jobs->lastItem(),
            ],
        ], 'Jobs retrieved successfully');
    }

    public function show(CareerJob $job)
    {
        if (!$job->is_active) {
            return $this->notFoundResponse('Job not available');
        }

        return $this->successResponse([
            'job' => new CareerJobResource($job->load(['department', 'location', 'jobType', 'roleSections'])),
        ], 'Job retrieved successfully');
    }
}
