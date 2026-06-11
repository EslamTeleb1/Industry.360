<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPackageController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
        {
            $perPage = max(1, min(100, $request->integer('per_page', 20)));
            $serviceType = $request->input('service_type', 'service');
            $serviceId = $request->input('service_id');
            $search = trim((string) $request->input('search', ''));

            $query = Package::orderByDesc('id')->with('serviceable'); // 👈 always load the correct relation

            if ($serviceId) {
                $query->where('service_id', $serviceId)
                    ->where('service_type', $serviceType);
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
                    'total' => $packages->total(),
                    'per_page' => $packages->perPage(),
                    'current_page' => $packages->currentPage(),
                    'last_page' => $packages->lastPage(),
                ],
            ], 'Packages retrieved successfully');
        }

    public function store(Request $request)
    {
        $serviceType = $request->input('service_type', 'service'); // default to 'service'
        $serviceId = $request->input('service_id');

        $data = $request->validate([
            'service_id' => [
                'required',
                'integer',
                $this->getServiceIdRule($serviceType),
            ],
            'service_type' => [
                'sometimes',
                'string',
                Rule::in(['service', 'contact_industry', 'contact_service', 'contact_solution']),
            ],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Ensure service_type is set
        $data['service_type'] = $serviceType;

        $package = Package::create($data);

        // Load appropriate relationship
        $this->loadPackageRelationship($package);

        return $this->createdResponse([
            'package' => new PackageResource($package),
        ], 'Package created successfully');
    }

    public function show(Package $package)
    {
        // Load appropriate relationship
        $this->loadPackageRelationship($package);

        return $this->successResponse([
            'package' => new PackageResource($package),
        ], 'Package retrieved successfully');
    }

    public function update(Request $request, Package $package)
    {
        $serviceType = $package->service_type ?? 'service';

        $data = $request->validate([
            'service_id' => [
                'sometimes',
                'integer',
                $this->getServiceIdRule($serviceType),
            ],
            'service_type' => [
                'sometimes',
                'string',
                Rule::in(['service', 'contact_industry', 'contact_service', 'contact_solution']),
            ],
            'title' => ['sometimes', 'array'],
            'title.en' => ['required_with:title', 'string'],
            'title.ar' => ['required_with:title', 'string'],
            'description' => ['sometimes', 'array'],
            'description.en' => ['required_with:description', 'string'],
            'description.ar' => ['required_with:description', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package->update($data);

        // Load appropriate relationship
        $this->loadPackageRelationship($package);

        return $this->successResponse([
            'package' => new PackageResource($package),
        ], 'Package updated successfully');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return $this->successResponse(null, 'Package deleted successfully');
    }

    /**
     * Get validation rule for service_id based on service_type
     */
    private function getServiceIdRule($serviceType)
    {
        return match ($serviceType) {
            'contact_industry' => Rule::exists('contact_industries', 'id'),
            'contact_service' => Rule::exists('contact_services', 'id'),
            'contact_solution' => Rule::exists('contact_solutions', 'id'),
            default => Rule::exists('services', 'id'),
        };
    }

    /**
     * Load appropriate relationship based on service_type
     */
    private function loadPackageRelationship(Package $package): void
    {
        match ($package->service_type ?? 'service') {
            'contact_industry' => $package->load('contactIndustry'),
            'contact_service' => $package->load('contactService'),
            'contact_solution' => $package->load('contactSolution'),
            default => $package->load('service'),
        };
    }
}
