<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use App\Traits\IndustryModuleHandler;
use Illuminate\Http\Request;

class AdminSolutionController extends Controller
{
    use ApiResponse, IndustryModuleHandler, DateFilterable;

    protected function getModuleType(): string
    {
        return 'solution';
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 15)));
        $sortDirection = strtolower($request->string('sort', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->input('search', ''));

        $items = $this->typeQuery($this->getModuleType())
            ->when($search !== '', function ($query) use ($search) {
                $query->where('title->en', 'like', "%{$search}%")
                    ->orWhere('title->ar', 'like', "%{$search}%")
                    ->orWhere('description->en', 'like', "%{$search}%")
                    ->orWhere('description->ar', 'like', "%{$search}%");
            })
            ->orderBy('created_at', $sortDirection);

        // Date filter
        $this->applyDateFilter($items, $request->input('date_filter'));

        $items = $items->paginate($perPage);

        return $this->successResponse([
            'items' => ServiceResource::collection($items->getCollection()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
        ], 'Solutions retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $this->validateIndustryItem($request, true);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('services', 'public');
        }

        $data['type'] = $this->getModuleType();
        $item = Service::create($data);

        return $this->createdResponse([
            'item' => new ServiceResource($item),
        ], 'Solution created successfully');
    }

    public function show(Service $item)
    {
        if ($item->type !== $this->getModuleType()) {
            return $this->notFoundResponse('Solution not found');
        }

        return $this->successResponse([
            'item' => new ServiceResource($item),
        ], 'Solution retrieved successfully');
    }

    public function update(Request $request, Service $item)
    {
        if ($item->type !== $this->getModuleType()) {
            return $this->notFoundResponse('Solution not found');
        }

        $data = $this->validateIndustryItem($request, false);

        if ($request->hasFile('img')) {
            $data['img_path'] = $request->file('img')->store('services', 'public');
        }

        $item->update($data);

        return $this->successResponse([
            'item' => new ServiceResource($item->fresh()),
        ], 'Solution updated successfully');
    }

    public function destroy(Service $item)
    {
        if ($item->type !== $this->getModuleType()) {
            return $this->notFoundResponse('Solution not found');
        }

        $item->delete();

        return $this->successResponse(null, 'Solution deleted successfully');
    }
}
