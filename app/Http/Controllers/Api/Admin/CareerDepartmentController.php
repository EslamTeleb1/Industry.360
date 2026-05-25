<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerDepartmentResource;
use App\Models\CareerDepartment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CareerDepartmentController extends Controller
{
    use ApiResponse;
    public function index()
    {
        return $this->successResponse([
            'departments' => CareerDepartmentResource::collection(CareerDepartment::query()->orderBy('id')->get()),
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
