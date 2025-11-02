<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Track;
use App\Models\Section;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\TrackRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackResource;
use App\Http\Traits\ApiResponserTrait;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreTrackRequest;

class AdminTrackController extends Controller
{
    use ApiResponserTrait;

    public function store(TrackRequest $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user || $user->role !== 'admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $section = Section::where('title', 'Tracks')->first();
        if (!$section) {
            return $this->errorResponse('Tracks section not found.', 404);
        }

        $data = $request->validated();
        if (empty($data['slug'])) {
            $slug = Str::slug($data['name']);
            $count = Track::where('slug', 'like', $slug . '%')->count();
            $data['slug'] = $count ? "{$slug}-" . ($count + 1) : $slug;
        }

        $exists = Track::where('name', $data['name'])
            ->Where('section_id', $section->id)
            ->exists();
        if ($exists) {
            return $this->errorResponse('Track with this name already exists in this section.', 422);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('tracks', 'public');
        }

        $data['status'] = Track::STATUS_DRAFT;
        $data['section_id'] = $section->id;

        $track = Track::create($data);

        return $this->successResponse(
            new TrackResource($track),
            'Track created successfully.'

        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(TrackRequest $request, $slug)
    {

        $track = Track::where('slug', $slug)->first();
        if (!$track) {
            return $this->errorResponse('Track not found', 404);
        }
        $data = $request->validated();
                // dd($data);

        if ($data['image']!== null) {
            Storage::disk('public')->delete($track->image);
            $data['image'] = $request->file('image')->store('tracks', 'public');
        }
        $track->update($data);
        return $this->successResponse(new TrackResource($track), 'Track updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $user = auth()->guard('api')->user();
        if (!$user || $user->role !== 'admin') {
            return $this->errorResponse('Unauthorized', 403);
        }
        $track = Track::where('slug', $slug)->first();
        if (!$track) {
            return $this->errorResponse('Track not found', 404);
        }
        if ($track->image) {
            Storage::disk('public')->delete($track->image);
        }
        $track->delete();
        return $this->successResponse(null, 'Track deleted successfully.', 200);
    }
    public function publish($slug)
    {
        $track = Track::where('slug', $slug)->first();
        if (!$track) {
            return $this->errorResponse('Track not found', 404);
        }
        if ($track->status === Track::STATUS_PUBLISHED) {
            return $this->errorResponse('Track is already published', 400);
        }
        $track->update(['status' => Track::STATUS_PUBLISHED]);
        return $this->successResponse($track, 'Track published successfully.');
    }

    public function draft(Request $request, $slug)
    {
        $track = Track::where('slug', $slug)->first();

        if (!$track) {
            return $this->errorResponse('Track not found', 404);
        }

        if ($track->status === Track::STATUS_DRAFT) {
            return $this->errorResponse('Track is already in draft', 400);
        }


        $track->update([
            'status' => Track::STATUS_DRAFT,
        ]);

        return $this->successResponse(
            $track,
            'Track moved to draft with rejection reason.'
        );
    }
}
