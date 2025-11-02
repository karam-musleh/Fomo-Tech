<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'mentor_id' => $this->mentor_id,
            'status' => Article::STATUS_UNDER_REVIEW,
            'content_type' => $this instanceof \App\Models\Article ? 'article' : 'resource',

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // بيانات المينتور
            'mentor' => $this->whenLoaded('mentor', function () {
                return [
                    'id' => $this->mentor->id,
                    'first_name' => $this->mentor->user->first_name,
                    'last_name' => $this->mentor->user->last_name,
                    'profile_photo' => $this->profile_photo
                        ? Storage::disk('public')->url($this->profile_photo)
                        : asset('uploads/admins/default.png'),

                ];
            }),
            'tracks' => $this->whenLoaded('tracks', function () {
                return $this->tracks->map(function ($track) {
                    return [
                        'id' => $track->id,
                        'name' => $track->name,
                    ];
                });
            }),
        ];
    }
}
