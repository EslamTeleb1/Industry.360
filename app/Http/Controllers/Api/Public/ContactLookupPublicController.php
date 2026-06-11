<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactLookupResource;
use App\Models\ContactService;
use App\Models\ContactSolution;
use App\Models\ContactIndustry;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ContactLookupPublicController extends Controller
{
    use ApiResponse;

    public function services()
    {
        $items = ContactService::orderBy('order')->orderBy('id')->with('packages')->get();

        return $this->successResponse([
            'services' => ContactLookupResource::collection($items),
        ], 'Contact services retrieved successfully');
    }

    public function showService(ContactService $item)
    {
        $item->load('packages');

        return $this->successResponse([
            'service' => new ContactLookupResource($item),
        ], 'Contact service retrieved successfully');
    }

    public function solutions()
    {
        $items = ContactSolution::orderBy('order')->orderBy('id')->with('packages')->get();

        return $this->successResponse([
            'solutions' => ContactLookupResource::collection($items),
        ], 'Contact solutions retrieved successfully');
    }

    public function showSolution(ContactSolution $item)
    {
        $item->load('packages');

        return $this->successResponse([
            'solution' => new ContactLookupResource($item),
        ], 'Contact solution retrieved successfully');
    }

    public function industries()
    {
        $items = ContactIndustry::orderBy('order')->orderBy('id')->with('packages')->get();

        return $this->successResponse([
            'industries' => ContactLookupResource::collection($items),
        ], 'Contact industries retrieved successfully');
    }

    public function showIndustry(ContactIndustry $item)
    {
        $item->load('packages');

        return $this->successResponse([
            'industry' => new ContactLookupResource($item),
        ], 'Contact industry retrieved successfully');
    }
}
