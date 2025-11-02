<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
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
        $rules = [
            'title'       => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'required|string',
            'slug'        => 'nullable|string|max:255',
            'details'     => 'required|string',
        ];

        // إذا حاب تضيف قواعد خاصة بالتحديث ممكن:
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules = [
                'title' => 'sometimes|string|max:255',
                'image'       => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
                'description' => 'sometimes|string',
                'slug'        => 'sometimes|string|max:255', // بدل image لأن slug نص وليس صورة
                'details'     => 'sometimes|string',
            ];
        }

        return $rules;
    }
}
