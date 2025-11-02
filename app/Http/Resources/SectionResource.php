<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ResourceResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'section' => [
                'id' => $this->id,
                'title' => $this->title,
                'image' => $this->image ? Storage::disk('public')->url($this->image) : asset('uploads/admins/images.png'),
                'description' => $this->description,
                'details' => $this->details,
                'slug' => $this->slug,
                'status'=>$this->status ,
                'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            ],
            'resources' => $this->whenLoaded('resources', function () {
                return ResourceResource::collection($this->resources)->values()->all();
            }, []),
        ];
    }
}
