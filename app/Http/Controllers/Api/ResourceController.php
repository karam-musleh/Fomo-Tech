<?php

namespace App\Http\Controllers\Api;

use App\Models\Resource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ResourceRequest;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\ResourceResource;

class ResourceController extends Controller
{
    use ApiResponserTrait;

    public function store(ResourceRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::guard('api')->user();

        if (!$user || $user->role !== 'mentor') {
            return $this->errorResponse('Only mentors can create resources.', 403);
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return $this->errorResponse('Mentor profile not found.', 404);
        }
        $trackIds   = $validated['track_ids']   ?? [];
        $sectionIds = $validated['section_ids'] ?? [];

        if (empty($trackIds) && empty($sectionIds)) {
            return $this->errorResponse(
                'You must attach at least one track or one section to the resource.',
                422
            );
        }
        $validated['mentor_id'] = $mentor->id;
        // dd($validated['mentor_id']);
        if (!empty($validated['track_ids'])) {
            $mentorTrackCount = $mentor->tracks()
                ->whereIn('tracks.id', $validated['track_ids'])
                ->count();

            if ($mentorTrackCount !== count($validated['track_ids'])) {
                return $this->errorResponse(
                    'You can only attach resources to your own tracks.',
                    403
                );
            }
        }

        $baseSlug = Str::slug($validated['name']);
        $slugExistsCount = Resource::where('slug', 'like', "{$baseSlug}%")->count();
        $validated['slug'] = $slugExistsCount ? "{$baseSlug}-" . ($slugExistsCount + 1) : $baseSlug;

        $validated['status'] = Resource::STATUS_UNDER_REVIEW;
        $resource = Resource::create($validated);

        $resource->tracks()->syncWithoutDetaching($trackIds);
        $resource->sections()->syncWithoutDetaching($sectionIds);
        $resource->load(['sections', 'tracks', 'mentor.user']);
        // dd($resource);

        return $this->successResponse(
            new ResourceResource($resource),
            'Resource created successfully.',
            201
        );
    }


    public function accepted($slug)
    {
        $resource  = Resource::where('slug', $slug)->first();
        if (!$resource) {
            return $this->errorResponse('Resource Not Found', 404);
        }
        $resource->update([
            'status' => Resource::STATUS_ACCEPTED,
            'rejection_reason' => null,
        ]);
        return $this->successResponse(null, 'Resource Accepted Successfully', 200);
    }
    public function rejected(Request $request, $slug)
    {
        $resource  = Resource::where('slug', $slug)->first();
        if (!$resource) {
            return $this->errorResponse('Resource Not Found', 404);
        }
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);
        $resource->update([
            'status' => Resource::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);
        return $this->successResponse($resource, 'Resource Rejected Successfully', 201);
    }
    public function underReview($slug)
    {
        $resource  = Resource::where('slug', $slug)->first();
        if (!$resource) {
            return $this->errorResponse('Resource Not Found', 404);
        }
        $resource->update([
            'status' => Resource::STATUS_UNDER_REVIEW,
            'rejection_reason' => null,
        ]);
        return $this->successResponse(null, 'Resource Under Review Successfully', 200);
    }
    public function getThisWeeksResourceCount()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $count = Resource::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();
            return $this->successResponse($count , "this_weeks_resources");

    }
}
