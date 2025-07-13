<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('post')->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'visibility' => 'sometimes|in:public,connections,private',
            'status' => 'sometimes|in:draft,published'
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