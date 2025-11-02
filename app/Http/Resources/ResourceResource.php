<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'link'             => $this->link,
            'content_type' => $this instanceof \App\Models\Article ? 'article' : 'resource',
            'type'             => $this->type,
            'status'           => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'created_at'       => $this->created_at->format('d M Y'),
            'updated_at'       => $this->updated_at->format('d M Y'),

            // ✅ المينتور
            'mentor' => $this->whenLoaded('mentor', function () {
                return [
                    'id' => $this->mentor->id,
                    'first_name' => $this->mentor->user->first_name ?? null,
                    'last_name'  => $this->mentor->user->last_name ?? null,
                    'profile_photo' => $this->profile_photo
                        ? Storage::disk('public')->url($this->profile_photo)
                        : asset('uploads/admins/default.png'),

                ];
            }),

            // ✅ السكاشن
            'sections' => $this->whenLoaded('sections', function () {
                return $this->sections->map(fn($section) => [
                    'id'    => $section->id,
                    'title' => $section->title,
                ]);
            }),

            // ✅ التراكات
            'tracks' => $this->whenLoaded('tracks', function () {
                return $this->tracks->map(fn($track) => [
                    'id'   => $track->id,
                    'name' => $track->name,
                ]);
            }),
        ];
    }
}
