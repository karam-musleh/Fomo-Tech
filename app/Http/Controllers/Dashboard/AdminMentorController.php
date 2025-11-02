<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\MentorResource;
use App\Http\Traits\ApiResponserTrait;

use function Laravel\Prompts\select;

class AdminMentorController extends Controller
{
    use ApiResponserTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentors = User::where('role', 'mentor')
        ->select("id","first_name", "last_name", "email", "profile_photo")
        ->get();
        
        if ($mentors->isEmpty()) {
            return $this->errorResponse('No mentors found', 404);
        }
        return $this->successResponse(MentorResource::collection($mentors), 'mentors retrieved successfully', 200);
    }

    public function show($id)
    {
        //
        $mentor = User::where('role', 'mentor')->find($id);
        if (!$mentor) {
            return $this->errorResponse('mentor not found', 404);
        }
        return $this->successResponse(new UserResource($mentor), 'mentor retrieved successfully', 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $mentor = User::where('role', 'mentor')->find($id);
        if (!$mentor) {
            return $this->errorResponse('mentor not found', 404);
        }
        $mentor->delete();
        return $this->successResponse(null, 'mentor deleted successfully', 200);
    }
}
