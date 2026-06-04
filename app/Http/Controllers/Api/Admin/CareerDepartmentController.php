<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerDepartmentResource;
use App\Models\CareerDepartment;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class CareerDepartmentController extends Controller
{
    use ApiResponse, DateFilterable;
    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 15)));
        $sortDirection = strtolower($request->string('sort', 'asc')->toString()) === 'desc' ? 'desc' : 'asc';
        $search = trim((string) $request->input('search', ''));

        $departments = CareerDepartment::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name->en', 'like', "%{$search}%")
                        ->orWhere('name->ar', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', $sortDirection);

        // Date filter
        $this->applyDateFilter($departments, $request->input('date_filter'));

        $departments = $departments->paginate($perPage);

        return $this->successResponse([
            'departments' => CareerDepartmentResource::collection($departments->getCollection()),
            'pagination' => [
                'url' => $departments->url($departments->currentPage()),
                'current_page' => $departments->currentPage(),
                'last_page' => $departments->lastPage(),
                'per_page' => $departments->perPage(),
                'total' => $departments->total(),
                'from' => $departments->firstItem(),
                'to' => $departments->lastItem(),
            ],
        ], 'Departments retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
        ]);

        $department = CareerDepartment::create($data);

        return $this->createdResponse([
            'department' => new CareerDepartmentResource($department),
        ], 'Department created successfully');
    }

    public function update(Request $request, CareerDepartment $department)
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
        ]);

        $department->update($data);

        return $this->successResponse([
            'department' => new CareerDepartmentResource($department->fresh()),
        ], 'Department updated successfully');
    }

    public function destroy(CareerDepartment $department)
    {
        $department->delete();

        return $this->successResponse(null, 'Department deleted successfully');
    }
}
