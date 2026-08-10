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
            'file' => [
                'required',
                'file',
                'max:512000', // 500MB in KB
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowedExtensions = ['pdf', 'ppt', 'pptx', 'mp4', 'mov'];
                    
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail('The file must be a PDF, PowerPoint presentation (ppt, pptx) or video (mp4, mov).');
                    }
                },
            ],
            'category_id' => ['nullable', 'exists:categories,id'],
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
            'file.mimes' => 'The file must be a PDF, PowerPoint presentation (ppt, pptx) or video (mp4, mov).',
            'file.max' => 'The file size must not exceed 500MB.',
            'category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
