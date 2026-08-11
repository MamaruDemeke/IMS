<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:4', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:4000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'category' => ['required', 'string', 'min:2', 'max:100'],
            'department_id' => ['required', 'exists:departments,id'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,txt,doc,docx', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a short title for the ticket.',
            'description.min' => 'Please provide at least 10 characters describing the issue.',
            'department_id.exists' => 'Please select a valid department.',
            'attachment.mimes' => 'Only PDF, image, text, or Word files are allowed.',
            'attachment.max' => 'Attachment size must be 2 MB or less.',
        ];
    }
}
