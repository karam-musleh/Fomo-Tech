<?php

namespace App\Http\Controllers\api;

use App\Models\Track;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrackRequest;
use App\Http\Resources\TrackResource;
use App\Http\Traits\ApiResponserTrait;

class TrackController extends Controller
{
    use ApiResponserTrait;

    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);

        $query = Track::with([
            'mentors.user:id',
            'resources:id,name',
            'articles:id,title,mentor_id'
        ])->select('id', 'name', 'image', 'description', 'status');

        $user = auth()->guard('api')->user();

        if (!$user || $user->role !== 'admin') {
            $query->where('status' , Track::STATUS_PUBLISHED);
        }
        $tracks = $query->paginate($per_page);

        return $this->successResponse(TrackResource::collection($tracks));
    }
    //show
    public function show($slug)
    {
        $user = auth()->guard('api')->user();
        $query =  Track::with(['mentors.user', 'resources', 'articles:id'])->where('slug', $slug);

        if (!$user || $user->role !== 'admin') {
            $query->where('status',Track::STATUS_PUBLISHED);
        }
        $track = $query->first();

        if (!$track) {
            return $this->errorResponse('Track not found', 404);
        }
        return $this->successResponse(new TrackResource($track), 'Track retrieved successfully.');
    }
}
