<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        'first_name'    => 'required|string|max:255',
        'last_name'     => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email',
        'role'          => 'required|string|in:mentor,student',
        'password'      => 'required|string|min:8|confirmed',
        'date_of_birth' => 'nullable|date',
        'pronoun'       => 'required|string|max:50',
        'major'         => 'required|string|max:50',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        'goals'         => 'required|string|max:255',
        'bio'           => 'required|string|max:1000',
        'linkedin_url'  => 'nullable|url|max:255',
    ];

    //  هنا الشرط: لو الدور Mentor أضيف القواعد الخاصة فيه
    if ($this->input('role') === 'mentor') {
        $rules = array_merge($rules, [
            'welcome_statement'   => 'required|string|max:500',
            'years_of_experience' => 'required|integer|min:0',
            'tracks'              => 'sometimes|array',
            'tracks.*'            => 'integer|exists:tracks,id',
            'skills'              => 'sometimes|array',
            'skills.*'            => 'string|max:255',
        ]);
    }

    return $rules;
}

}
