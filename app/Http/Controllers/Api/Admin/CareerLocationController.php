<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerLocationResource;
use App\Models\CareerLocation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CareerLocationController extends Controller
{
    use ApiResponse;
    public function index()
    {
        return $this->successResponse([
            'locations' => CareerLocationResource::collection(CareerLocation::query()->orderBy('id')->get()),
        ], 'Locations retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
        ]);

        $location = CareerLocation::create($data);

        return $this->createdResponse([
            'location' => new CareerLocationResource($location),
        ], 'Location created successfully');
    }

    public function update(Request $request, CareerLocation $location)
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
        ]);

        $location->update($data);

        return $this->successResponse([
            'location' => new CareerLocationResource($location->fresh()),
        ], 'Location updated successfully');
    }

    public function destroy(CareerLocation $location)
    {
        $location->delete();

        return $this->successResponse(null, 'Location deleted successfully');
    }
}
