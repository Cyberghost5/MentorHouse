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
            'headline'            => ['nullable', 'string', 'max:100'],
            'bio'                 => ['nullable', 'string', 'max:2000'],
            'expertise'           => ['required', 'array', 'min:1'],
            'expertise.*'         => ['string', 'max:60'],
            'availability'        => ['required', 'in:open,closed'],
            'session_type'        => ['required', 'in:free,paid,project_based'],
            'one_time_fee'        => ['nullable', 'numeric', 'min:0', 'max:999999999.99', 'required_if:session_type,paid'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'profile_photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_photo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'one_time_fee.required_if' => 'A one-time fee is required for paid sessions.',
        ];
    }
}
