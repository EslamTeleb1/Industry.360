<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerJobApplicationResource;
use App\Models\CareerJobApplication;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class CareerJobApplicationController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = CareerJobApplication::with(['job', 'job.department', 'job.location', 'job.jobType'])
            ->orderByDesc('id');

        if ($request->filled('job_id')) {
            $query->where('career_job_id', $request->input('job_id'));
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', "%{$request->input('email')}%");
        }

        // Text search across name, email, phone
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Date filter
        $this->applyDateFilter($query, $request->input('date_filter'));

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
