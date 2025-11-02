<?php

namespace App\Http\Controllers\Api;


use Exception;
// use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiResponserTrait;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    use ApiResponserTrait;

    public function profile()
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        return $this->successResponse(new UserResource($user), 'User profile retrieved successfully', 200);
    }



    public function update(UpdateRequest $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('uploads', 'public');
        }
        if ($user->role === 'mentor' && $user->mentor) {
            if (isset($data['welcome_statement'])) {
                $user->mentor->welcome_statement = $data['welcome_statement'];
            }
            if (isset($data['years_of_experience'])) {
                $user->mentor->years_of_experience = $data['years_of_experience'];
            }
            $user->mentor->save();

            if (isset($data['tracks'])) {
                $user->mentor->tracks()->sync($data['tracks']);
            }
            if (isset($data['skills'])) {
                $user->mentor->skills()->sync($data['skills']);
            }
        }

        $user->update($data);

        return $this->successResponse(
            new UserResource($user),
            'User updated successfully',
            200
        );
    }

    public function delete()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        if ($user->role === 'admin') {
            return $this->errorResponse('Admin user cannot be deleted', 403);
        }

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        if ($user->role === 'mentor' && $user->mentor) {
            $user->mentor->tracks()->detach();
            $user->mentor->skills()->detach();
            $user->mentor->delete();
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully', 204);
    }
}
