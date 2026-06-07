<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminPackageController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $serviceId = $request->input('service_id');
        $search = trim((string) $request->input('search', ''));

        $query = Package::with('service')->orderByDesc('id');

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        $packages = $query->paginate($perPage);

        return $this->successResponse([
            'packages' => PackageResource::collection($packages->getCollection()),
            'pagination' => [
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'per_page' => $packages->perPage(),
                'total' => $packages->total(),
            ],
        ], 'Packages retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package = Package::create($data);
        $package->load('service');

        return $this->createdResponse([
            'package' => new PackageResource($package),
        ], 'Package created successfully');
    }

    public function show(Package $package)
    {
        $package->load('service');

        return $this->successResponse([
            'package' => new PackageResource($package),
        ], 'Package retrieved successfully');
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'service_id' => ['sometimes', 'integer', 'exists:services,id'],
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package->update($data);
        $package->load('service');

        return $this->successResponse([
            'package' => new PackageResource($package),
        ], 'Package updated successfully');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return $this->successResponse(null, 'Package deleted successfully');
    }
}
