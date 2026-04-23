<?php

namespace App\Http\Requests;

use App\Models\MentorProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSessionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isMentee();
    }

    public function rules(): array
    {
        // Derive allowed session_type from the target mentor's profile
        $mentor = \App\Models\User::where('id', $this->route('mentor'))
            ->where('role', 'mentor')
            ->first();

        $allowedType = $mentor?->mentorProfile?->session_type ?? 'free';

        return [
            'mentor_id'           => ['required', 'integer', 'exists:users,id'],
            'session_type'        => ['required', Rule::in([$allowedType])],
            'message'             => ['nullable', 'string', 'max:2000'],
            'proposed_date'       => ['required', 'date', 'after:now'],
            'fee_amount'          => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'project_description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
