<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponserTrait;
use App\Models\Mentor;

class MentorController extends Controller
{
    use ApiResponserTrait;

    public function mentors()
    {
        $per_page = request()->input('per_page', 10);

        $mentors = User::with(['mentor.tracks', 'mentor.skills'])
            ->where('role', 'mentor')
            ->select('id', 'first_name', 'last_name', 'role', 'profile_photo')
            ->paginate($per_page);
        return $this->successResponse(
            UserResource::collection($mentors),
            'Mentors retrieved successfully',
            200
        );
    }

    public function mentor_profile($id)
    {
        $mentor = User::where('role', 'mentor')
        ->where('id', $id)
        ->first();
        if (!$mentor) {
            return $this->errorResponse('Mentor not found', 404);
        }
        return $this->successResponse( new UserResource($mentor), 'Users retrieved successfully', 200);
    }
    
}
