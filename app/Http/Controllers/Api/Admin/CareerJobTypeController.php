<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerJobTypeResource;
use App\Models\CareerJobType;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CareerJobTypeController extends Controller
{
    use ApiResponse;
    public function index()
    {
        return $this->successResponse([
            'job_types' => CareerJobTypeResource::collection(CareerJobType::query()->orderBy('id')->get()),
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
