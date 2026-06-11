<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactLookupResource;
use App\Models\ContactMessage;
use App\Models\ContactIndustry;
use App\Models\ContactService;
use App\Models\ContactSolution;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    use ApiResponse;

    public function lookups()
    {
        return $this->successResponse([
            'industries' => ContactLookupResource::collection(
                ContactIndustry::orderBy('order')->orderBy('id')->with('packages')->get()
            ),
            'services' => ContactLookupResource::collection(
                ContactService::orderBy('order')->orderBy('id')->with('packages')->get()
            ),
            'solutions' => ContactLookupResource::collection(
                ContactSolution::orderBy('order')->orderBy('id')->with('packages')->get()
            ),
        ], 'Contact lookups retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'industry_id' => [
                'required',
                'integer',
                Rule::exists('contact_industries', 'id'),
            ],
            'service_id' => [
                'nullable',
                'required_without:solution_id',
                'integer',
                Rule::exists('contact_services', 'id'),
            ],
            'solution_id' => [
                'nullable',
                'required_without:service_id',
                'integer',
                Rule::exists('contact_solutions', 'id'),
            ],
            'package_id' => [
                'required',
                'integer',
                Rule::exists('packages', 'id')->where(function ($query) {
                    // Package must belong to a contact lookup (industry, service, or solution)
                    $query->whereIn('service_type', ['contact_industry', 'contact_service', 'contact_solution']);
                }),
            ],
            'description' => ['required', 'string'],
        ]);

        ContactMessage::create($data);

        return $this->createdResponse(null, 'Contact message submitted successfully');
    }
}
