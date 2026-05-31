<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use App\Traits\IndustryModuleHandler;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    use ApiResponse, IndustryModuleHandler;

    protected function getModuleType(): string
    {
        return 'industry';
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 12)));
        $sortDirection = strtolower($request->string('sort', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->input('search', ''));

        $items = $this->typeQuery($this->getModuleType())
            ->when($search !== '', function ($query) use ($search) {
                $query->where('title->en', 'like', "%{$search}%")
                    ->orWhere('title->ar', 'like', "%{$search}%")
                    ->orWhere('description->en', 'like', "%{$search}%")
                    ->orWhere('description->ar', 'like', "%{$search}%");
            })
            ->orderBy('created_at', $sortDirection)
            ->paginate($perPage);

        return $this->successResponse([
            'industries' => ServiceResource::collection($items->getCollection()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
        ], 'Industries retrieved successfully');
    }

    public function show(Service $item)
    {
        if ($item->type !== $this->getModuleType()) {
            return $this->notFoundResponse('Industry not found');
        }

        return $this->successResponse([
            'industry' => new ServiceResource($item),
        ], 'Industry retrieved successfully');
    }
}
