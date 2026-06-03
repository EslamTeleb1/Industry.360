<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerJobApplicationResource;
use App\Models\CareerJobApplication;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CareerJobApplicationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));

        $query = CareerJobApplication::with(['job', 'job.department', 'job.location', 'job.jobType'])
            ->orderByDesc('id');

        if ($request->filled('job_id')) {
            $query->where('career_job_id', $request->input('job_id'));
        }
        if ($request->filled('email')) {
            $query->where('email', $request->input('email'));
        }

        $applications = $query->paginate($perPage);

        return $this->successResponse([
            'applications' => CareerJobApplicationResource::collection($applications->getCollection()),
            'pagination' => [
                'url' => $applications->url($applications->currentPage()),
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'from' => $applications->firstItem(),
                'to' => $applications->lastItem(),
            ],
        ], 'Applicants retrieved successfully');
    }
}
