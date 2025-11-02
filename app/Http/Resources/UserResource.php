<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array

    {
        $data = [
            'id'             => $this->id,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'email'          => $this->email,
            'role'           => $this->role,
            'date_of_birth' => optional($this->date_of_birth)->format('Y-m-d'),
            'pronoun'        => $this->pronoun,
            'major'          => $this->major,
            'profile_photo' => $this->profile_photo
                ? Storage::disk('public')->url($this->profile_photo)
                : asset('uploads/admins/default.png'),
            'goals'          => $this->goals,
            'bio'            => $this->bio,
            'linkedin_url'   => $this->linkedin_url,
            'created_at'    => optional($this->created_at)->format('d M Y'),
            'updated_at'    => optional($this->updated_at)->format('d M Y'),
        ];
        if ($this->role == 'mentor' && $this->mentor) {
            $data['mentor'] = [
                'welcome_statement'   => $this->mentor->welcome_statement,
                'years_of_experience' => $this->mentor->years_of_experience,
                'tracks'              => $this->mentor->tracks->pluck('name'), // assuming tracks table has "name"
                'skills'              => $this->mentor->skills ?? [], // assuming skills table has "name"
            ];
        }

        return $data;
    }
}
