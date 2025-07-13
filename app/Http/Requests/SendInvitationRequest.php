<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_id.required' => 'Recipient is required',
            'recipient_id.exists' => 'The selected recipient does not exist',
            'message.max' => 'Message cannot exceed 500 characters'
        ];
    }
}