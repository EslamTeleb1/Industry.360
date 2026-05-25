<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CareerJobApplicationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'career_job_id' => $this->career_job_id,
            'job' => new CareerJobResource($this->whenLoaded('job')),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'years_of_experience' => $this->years_of_experience,
            'start_date' => optional($this->start_date)->toDateString(),
            'expected_salary' => $this->expected_salary,
            'linkedin_profile' => $this->linkedin_profile,
            'cv_url' => $this->cv_url,
            'cover_letter' => $this->cover_letter,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
