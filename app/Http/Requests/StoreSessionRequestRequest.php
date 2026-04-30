<?php

namespace App\Http\Requests;

use App\Models\MentorProfile;
use App\Models\SessionRequest;
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
            'mentor_id'           => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('session_requests', 'mentor_id')->where(function ($query) {
                    return $query
                        ->where('mentee_id', $this->user()->id)
                        ->where('status', SessionRequest::STATUS_PENDING);
                }),
            ],
            'session_type'        => ['required', Rule::in([$allowedType])],
            'message'             => ['nullable', 'string', 'max:2000'],
            'proposed_date'       => ['required', 'date', 'after:now'],
            'fee_amount'          => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'project_description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'mentor_id.unique' => 'You already have a pending request with this mentor. Wait for a response before requesting another session.',
        ];
    }
}
