<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSessionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $sessionRequest = $this->route('sessionRequest');

        return $this->user()->id === $sessionRequest->mentor_id
            && $sessionRequest->isPending();
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            redirect()->route('session-requests.index')
                ->with('error', 'You can only accept or decline requests that are still pending and assigned to you.')
        );
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['accepted', 'declined'])],
        ];
    }
}
