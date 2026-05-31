<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerJobResource;
use App\Models\CareerJob;
use App\Models\CareerJobRoleSection;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CareerJobController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 15)));
        $sortDirection = strtolower($request->string('sort', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->input('search', ''));

        $jobs = CareerJob::query()
            ->with(['department', 'location', 'jobType', 'roleSections'])
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
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
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

    public function store(Request $request)
    {
        $data = $this->validateJob($request);

        return DB::transaction(function () use ($request, $data) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('careers/jobs', 'public');
            }

            $job = CareerJob::create([
                'department_id' => $data['department_id'],
                'location_id' => $data['location_id'],
                'job_type_id' => $data['job_type_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'image_path' => $imagePath,
                'is_active' => $data['is_active'],
            ]);

            $this->syncRoleSections($job, $data['role_sections'] ?? []);

            return $this->createdResponse([
                'job' => new CareerJobResource($job->load(['department', 'location', 'jobType', 'roleSections'])),
            ], 'Job created successfully');
        });
    }

    public function show(CareerJob $job)
    {
        return $this->successResponse([
            'job' => new CareerJobResource($job->load(['department', 'location', 'jobType', 'roleSections'])),
        ], 'Job retrieved successfully');
    }

    public function update(Request $request, CareerJob $job)
    {
        $data = $this->validateJob($request, $job->id);

        return DB::transaction(function () use ($request, $job, $data) {
            if ($request->hasFile('image')) {
                if ($job->image_path) {
                    Storage::disk('public')->delete($job->image_path);
                }
                $job->image_path = $request->file('image')->store('careers/jobs', 'public');
            }

            $job->update([
                'department_id' => $data['department_id'],
                'location_id' => $data['location_id'],
                'job_type_id' => $data['job_type_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'is_active' => $data['is_active'],
            ]);

            $job->roleSections()->delete();
            $this->syncRoleSections($job, $data['role_sections'] ?? []);

            return $this->successResponse([
                'job' => new CareerJobResource($job->load(['department', 'location', 'jobType', 'roleSections'])),
            ], 'Job updated successfully');
        });
    }

    public function destroy(CareerJob $job)
    {
        if ($job->image_path) {
            Storage::disk('public')->delete($job->image_path);
        }

        $job->delete();

        return $this->successResponse(null, 'Job deleted successfully');
    }

    private function validateJob(Request $request, ?int $jobId = null): array
    {
        return $request->validate([
            'department_id' => ['required', 'integer', 'exists:career_departments,id'],
            'location_id' => ['required', 'integer', 'exists:career_locations,id'],
            'job_type_id' => ['required', 'integer', 'exists:career_job_types,id'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['required', 'boolean'],
            'role_sections' => ['nullable', 'array'],
            'role_sections.*.title' => ['required_with:role_sections', 'array'],
            'role_sections.*.title.en' => ['required_with:role_sections', 'string'],
            'role_sections.*.title.ar' => ['required_with:role_sections', 'string'],
            'role_sections.*.description' => ['required_with:role_sections', 'array'],
            'role_sections.*.description.en' => ['required_with:role_sections', 'string'],
            'role_sections.*.description.ar' => ['required_with:role_sections', 'string'],
            'role_sections.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function syncRoleSections(CareerJob $job, array $sections): void
    {
        foreach ($sections as $section) {
            CareerJobRoleSection::create([
                'career_job_id' => $job->id,
                'title' => $section['title'],
                'description' => $section['description'],
                'sort_order' => $section['sort_order'] ?? 0,
            ]);
        }
    }
}
