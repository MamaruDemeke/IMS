<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:3', 'max:4000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,txt,doc,docx', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Please enter a message before sending.',
            'message.min' => 'The message must contain at least 3 characters.',
            'attachment.mimes' => 'Only PDF, image, text, or Word files are allowed.',
            'attachment.max' => 'Attachment size must be 2 MB or less.',
        ];
    }
}
