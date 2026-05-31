<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;

class ServicePublicController extends Controller
{
    use ApiResponse;

    public function services()
    {
        return $this->successResponse([
            'services' => ServiceResource::collection($this->orderedQuery('service')->get()),
        ], 'Services retrieved successfully');
    }

    public function showService(Service $service)
    {
        if ($service->type !== 'service') {
            return $this->notFoundResponse('Service not available');
        }

        return $this->successResponse([
            'service' => new ServiceResource($service),
        ], 'Service retrieved successfully');
    }

    public function solutions()
    {
        return $this->successResponse([
            'solutions' => ServiceResource::collection($this->orderedQuery('solution')->get()),
        ], 'Solutions retrieved successfully');
    }

    public function showSolution(Service $service)
    {
        if ($service->type !== 'solution') {
            return $this->notFoundResponse('Solution not available');
        }

        return $this->successResponse([
            'solution' => new ServiceResource($service),
        ], 'Solution retrieved successfully');
    }

    private function orderedQuery(string $type)
    {
        return Service::query()
            ->where('type', $type)
            ->orderBy('service_order')
            ->orderBy('id');
    }
}