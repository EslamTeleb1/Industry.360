<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'industry_id' => ['required', 'integer', 'exists:services,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'solution_id' => ['required', 'integer', 'exists:services,id'],
            'description' => ['required', 'string'],
        ]);

        $message = ContactMessage::create($data);

        return $this->createdResponse(null, 'Contact message submitted successfully');
    }
}
