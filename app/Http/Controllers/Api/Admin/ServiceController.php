<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->successResponse([
            'services' => ServiceResource::collection($this->orderedQuery('service')->get()),
            'solutions' => ServiceResource::collection($this->orderedQuery('solution')->get()),
        ], 'Content items retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $this->validateService($request, true);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('services', 'public');
        }

        $service = Service::create($data);

        return $this->createdResponse([
            'service' => new ServiceResource($service),
        ], 'Service created successfully');
    }

    public function show(Service $service)
    {
        return $this->successResponse([
            'service' => new ServiceResource($service),
        ], 'Content item retrieved successfully');
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validateService($request, false);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('services', 'public');
        }

        $service->update($data);

        return $this->successResponse([
            'service' => new ServiceResource($service->fresh()),
        ], 'Content item updated successfully');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return $this->successResponse(null, 'Content item deleted successfully');
    }

    private function validateService(Request $request, bool $isCreate): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'in:service,solution'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'img' => [$isCreate ? 'required' : 'nullable', 'image', 'max:2048'],
            'service_order' => ['required', 'integer', 'min:1'],
        ]);
    }

    private function orderedQuery(string $type)
    {
        return Service::query()
            ->where('type', $type)
            ->orderBy('service_order')
            ->orderBy('id');
    }
}