<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerJobApplicationResource;
use App\Models\CareerJob;
use App\Models\CareerJobApplication;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CareerApplicationController extends Controller
{
    use ApiResponse;
    public function apply(Request $request, CareerJob $job)
    {
        if (!$job->is_active) {
            return $this->notFoundResponse('Job not available');
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'start_date' => ['nullable', 'date'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'linkedin_profile' => ['nullable', 'url', 'max:255'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'cover_letter' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $cvPath = $request->file('cv')->store('careers/cv', 'public');

        $coverLetterPath = null;
        if ($request->hasFile('cover_letter')) {
            $coverLetterPath = $request->file('cover_letter')->store('careers/cover_letters', 'public');
        }

        $application = CareerJobApplication::create([
            'career_job_id' => $job->id,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'years_of_experience' => $data['years_of_experience'],
            'start_date' => $data['start_date'] ?? null,
            'expected_salary' => $data['expected_salary'] ?? null,
            'linkedin_profile' => $data['linkedin_profile'] ?? null,
            'cv_path' => $cvPath,
            'cover_letter_path' => $coverLetterPath,
        ]);

        return $this->createdResponse([
            'application' => new CareerJobApplicationResource($application),
        ], 'Application submitted successfully');
    }
}
