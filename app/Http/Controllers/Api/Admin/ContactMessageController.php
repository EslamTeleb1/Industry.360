<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Traits\ApiResponse;
use App\Traits\DateFilterable;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    use ApiResponse, DateFilterable;

    public function index(Request $request)
    {
        $perPage = max(1, min(100, $request->integer('per_page', 20)));
        $search = trim((string) $request->input('search', ''));

        $query = ContactMessage::with(['industry', 'service', 'solution', 'package'])
            ->orderByDesc('id');

        if ($request->filled('email')) {
            $query->where('email', 'like', "%{$request->input('email')}%");
        }

        if ($request->filled('company_name')) {
            $query->where('company_name', 'like', "%{$request->input('company_name')}%");
        }

        // Text search across name, company, email, phone
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Date filter
        $this->applyDateFilter($query, $request->input('date_filter'));

        $messages = $query->paginate($perPage);

        return $this->successResponse([
             ContactMessageResource::collection($messages->getCollection()),
            'pagination' => [
                'url' => $messages->url($messages->currentPage()),
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'from' => $messages->firstItem(),
                'to' => $messages->lastItem(),
            ],
        ], 'Contact messages retrieved successfully');
    }

    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->load(['industry', 'service', 'solution', 'package']);

        return $this->successResponse([
            'contact_message' => new ContactMessageResource($contactMessage),
        ], 'Contact message retrieved successfully');
    }
}
