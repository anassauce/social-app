<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAIPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => 'required|string|max:500',
            'style' => 'in:professional,casual,informative,creative',
            'length' => 'in:short,medium,long'
        ];
    }

    public function messages(): array
    {
        return [
            'prompt.required' => 'AI prompt is required',
            'prompt.max' => 'AI prompt cannot exceed 500 characters',
            'style.in' => 'Style must be professional, casual, informative, or creative',
            'length.in' => 'Length must be short, medium, or long'
        ];
    }
}