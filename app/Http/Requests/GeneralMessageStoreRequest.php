<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralMessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'reply_to_id' => ['nullable', 'integer', 'exists:general_messages,id'],
            'message' => ['required', 'string', 'min:1', 'max:4000'],
        ];
    }
}
