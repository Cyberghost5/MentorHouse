<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMentorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isMentor();
    }

    public function rules(): array
    {
        return [
            'expertise'           => ['required', 'array', 'min:1'],
            'expertise.*'         => ['string', 'max:60'],
            'availability'        => ['required', 'in:open,closed'],
            'session_type'        => ['required', 'in:free,paid,project_based'],
            'hourly_rate'         => ['nullable', 'numeric', 'min:0', 'max:9999.99', 'required_if:session_type,paid'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'hourly_rate.required_if' => 'An hourly rate is required for paid sessions.',
        ];
    }
}
