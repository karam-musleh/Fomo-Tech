<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'         => 'required|string|max:255',
                'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'description'  => 'required|string|max:255',
                'introduction' => 'nullable|string|max:255',
                'content'      => 'required|string|max:255',
            ];
        }

        return [
            'name'         => 'sometimes|string|max:255',
            'image'        => 'sometimes|image|mimes:jpg,jpeg,png|max:5120',
            'description'  => 'sometimes|string|max:255',
            'introduction' => 'sometimes|string|max:255',
            'content'      => 'sometimes|string|max:255',
        ];
    }
}
