<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use App\Traits\IndustryModuleHandler;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    use ApiResponse, IndustryModuleHandler;

    protected function getModuleType(): string
    {
        return 'solution';
    }

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 12)));
        $sortDirection = strtolower($request->string('sort', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->input('search', ''));

        $items = $this->typeQuery($this->getModuleType())
            ->with('packages')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('title->en', 'like', "%{$search}%")
                    ->orWhere('title->ar', 'like', "%{$search}%")
                    ->orWhere('description->en', 'like', "%{$search}%")
                    ->orWhere('description->ar', 'like', "%{$search}%");
            })
            ->orderBy('created_at', $sortDirection)
            ->paginate($perPage);

        return $this->successResponse([
            'solutions' => ServiceResource::collection($items->getCollection()),
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

    public function show(Service $item)
    {
        if ($item->type !== $this->getModuleType()) {
            return $this->notFoundResponse('Solution not found');
        }

        $item->load('packages');

        return $this->successResponse([
            'solution' => new ServiceResource($item),
        ], 'Solution retrieved successfully');
    }
}
