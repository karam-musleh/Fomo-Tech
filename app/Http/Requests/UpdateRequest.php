<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     *
     * @return array<string,
     */
    public function rules(): array
    {
        $rules = [
            'first_name'    => 'sometimes|string|max:255',
            'last_name'     => 'sometimes|string|max:255',
            'role'          => 'sometimes|string|in:mentor,student',
            'password'      => 'sometimes|string|min:8|confirmed',
            'date_of_birth' => 'sometimes|date|nullable',
            'pronoun'       => 'sometimes|string|max:50|nullable',
            'major'         => 'sometimes|string|max:50|nullable',
            'profile_photo' => 'sometimes|image|mimes:jpg,jpeg,png|max:5120',
            'goals'         => 'sometimes|string|max:255|nullable',
            'bio'           => 'sometimes|string|max:1000|nullable',
            'linkedin_url'  => 'sometimes|url|max:255|nullable',
        ];
        
        $user = $this->user('api')??$this->user();
        if ( $user && $user->role === 'mentor') {
            $rules = array_merge($rules, [
                'welcome_statement'   => 'sometimes|string|max:500|nullable',
                'years_of_experience' => 'sometimes|integer|min:0|nullable',
                'tracks'              => 'sometimes|array',
                'tracks.*'            => 'integer|exists:tracks,id',
                'skills'              => 'sometimes|array',
                'skills.*'            => 'integer|exists:skills,id',
            ]);
        }
        return $rules;
    }
}
