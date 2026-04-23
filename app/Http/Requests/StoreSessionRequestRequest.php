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
        // $this->route('mentor') returns the already-bound User model when route
        // model binding is active, so we handle both cases.
        $mentorParam = $this->route('mentor');
        $mentor = $mentorParam instanceof \App\Models\User
            ? $mentorParam
            : \App\Models\User::where('id', $mentorParam)->where('role', 'mentor')->first();

        $allowedType = $mentor?->mentorProfile?->session_type ?? 'free';

        return [
            'mentor_id'           => ['required', 'integer', 'exists:users,id'],
            'session_type'        => ['required', Rule::in([$allowedType])],
            'message'             => ['nullable', 'string', 'max:2000'],
            'proposed_date'       => ['required', 'date', 'after:now'],
            'fee_amount'          => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'project_description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
