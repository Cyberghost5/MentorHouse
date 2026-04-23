<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $sessionRequest = $this->route('sessionRequest');

        return $this->user()->id === $sessionRequest->mentor_id
            && $sessionRequest->isPending();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['accepted', 'declined'])],
        ];
    }
}
