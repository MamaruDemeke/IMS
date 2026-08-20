<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:open,in_progress,resolved,closed,pending_confirmation'],
            'priority' => ['nullable', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'response' => ['nullable', 'string', 'min:3', 'max:4000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please choose a ticket status.',
            'response.min' => 'The response must contain at least 3 characters.',
            'assigned_to.exists' => 'Please select a valid assigned user.',
        ];
    }
}
