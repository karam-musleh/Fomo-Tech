<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'type' => 'Blog',
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'slug' => $this->slug , 
            'mentor' => $this->whenLoaded('mentor', function () {
                return [
                    'id' => $this->mentor->id,
                    'first_name' => $this->mentor->user->first_name,
                    'last_name' => $this->mentor->user->last_name,
                    'profile_photo' => $this->mentor->user->profile_photo,
                    'tracks' => $this->mentor->tracks->pluck('name'),
                    // 'email' => $this->mentor->user->email,
                ];
            }, null),
            'section' => $this->whenLoaded('section', function () {
                return [
                    'id' => $this->section->id,
                    'title' => $this->section->title,
                ];
            }, null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
