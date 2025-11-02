<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrackRequest extends FormRequest
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

            'name'=>'sometimes|string|max:255',
            'image'=>'sometimes|image|mimes:jpg,jpeg,png|max:5120',
            'description'=>'sometimes|string|max:255',
            'introduction'=>'nullable|string|max:255',
            'content'=>'nullable|string|max:255',
        ];
    }
}
