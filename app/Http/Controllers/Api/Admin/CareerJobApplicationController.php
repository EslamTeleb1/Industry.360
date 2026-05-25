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
        $query = CareerJobApplication::with(['job', 'job.department', 'job.location', 'job.jobType'])
            ->orderByDesc('id');

        if ($request->filled('job_id')) {
            $query->where('career_job_id', $request->input('job_id'));
        }
        if ($request->filled('email')) {
            $query->where('email', $request->input('email'));
        }

        $applications = $query->paginate(20);

        return $this->successResponse([
            'applications' => CareerJobApplicationResource::collection($applications),
        ], 'Applicants retrieved successfully');
    }
}
