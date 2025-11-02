<?php

namespace App\Http\Controllers\Api;

use App\Models\Track;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponserTrait;

class FavoriteTrackController extends Controller
{
    use ApiResponserTrait;
    // add favorite track
    public function addToFavorites(Request $request, $trackId)
    {
        $user = $request->user('api') ?? Auth::guard('api')->user();
        if (!$user) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        $track = Track::find($trackId);
        if (!$track) {
            return $this->errorResponse('Track not found', 404);
        }
        if ($user->favoriteTracks()->where('track_id', $trackId)->exists()) {
            return $this->errorResponse('Track already in favorites', 409);
        }

        try {
            $user->favoriteTracks()->syncWithoutDetaching([$trackId]);

            return $this->successResponse('Track added to favorites.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }
    public function removeFromFavorites($trackId)
    {
        $user = Request()->user('api') ?? Auth::guard('api')->user();
        if (!$user) {
            return $this->errorResponse('Unauthenticated', 401);
        }
        $track = Track::find($trackId);
        if (!$track) {
            return $this->errorResponse('Track not found', 404);
        }
        if (!$user->favoriteTracks()->where('track_id', $trackId)->exists()) {
            return $this->errorResponse('Track is not in favorites', 400);
        }
        try {
            $user->favoriteTracks()->detach($trackId);

            return $this->successResponse('Track removed from favorites.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }
    public function getFavoriteTracks()
    {
        $user =  request()->user('api') ?? Auth::guard('api')->user();
        if (!$user) {
            return $this->errorResponse('Unauthenticated', 401);
        }
        $favoriteTracks = $user->favoriteTracks()->with('section')->get();
        return $this->successResponse($favoriteTracks, 200);

        
    }
}
