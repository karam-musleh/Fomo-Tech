<?php

namespace App\Http\Resources;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackResource extends JsonResource
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
            'name' => $this->name,
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'description' => $this->description,
            'introduction' => $this->introduction,
            'content' => $this->content,
            'slug'=> $this->slug ,
            'status' => Track::STATUS_DRAFT,
            'section_id' => $this->section_id,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'mentors' => $this->whenLoaded('mentors', function () {
                return $this->mentors->map(function ($mentor) {
                    return [
                        'id' => $mentor->id,
                        'first_name' => $mentor->user->first_name,
                        'last_name' => $mentor->user->last_name,
                        'email' => $mentor->user->email,
                    ];
                })->values()->all();
            }, []),
            'articles' => $this->whenLoaded('articles', function () {
                return ArticleResource::collection($this->articles)->values()->all();
            }, []),
            'resources' => $this->whenLoaded('resources', function () {
                return ResourceResource::collection($this->resources)->values()->all();
            }, []),


        ];
    }
}
