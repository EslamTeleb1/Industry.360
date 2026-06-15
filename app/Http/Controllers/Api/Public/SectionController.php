<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $sections = Section::query()->get();

        return $this->successResponse([
            'sections' => SectionResource::collection($sections),
        ], 'Sections retrieved');
    }

    public function show(Section $section)
    {
        return $this->successResponse([
            'section' => new SectionResource($section),
        ], 'Section retrieved');
    }
}
