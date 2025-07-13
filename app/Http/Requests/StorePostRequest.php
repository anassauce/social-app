<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'in:public,connections,private',
            'status' => 'in:draft,published'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Post title is required',
            'title.max' => 'Post title cannot exceed 255 characters',
            'content.required' => 'Post content is required',
            'visibility.in' => 'Visibility must be public, connections, or private',
            'status.in' => 'Status must be draft or published'
        ];
    }
}