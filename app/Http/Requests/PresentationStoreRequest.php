<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresentationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'mimes:ppt,pptx,mp4,mov', 'max:102400'], // 100MB in KB
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.mimes' => 'The file must be a PowerPoint presentation (ppt, pptx) or video (mp4, mov).',
            'file.max' => 'The file size must not exceed 100MB.',
        ];
    }
}
