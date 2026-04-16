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
            'body' => ['nullable', 'string', 'max:5000'],
            'file' => [
                'nullable', 
                'file', 
                'mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp,mp4', 
                'max:15360', // extended to 15mb to allow media
            ],
            'tags' => ['nullable', 'string'],
        ];
    }
}
