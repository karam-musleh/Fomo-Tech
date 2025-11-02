<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponserTrait;
use App\Models\User;
use App\Notifications\OtpUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class RegisterController extends Controller
{
    use ApiResponserTrait;

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('uploads', 'public');
            $user->profile_photo = $path;
        }
        $user->save();

        if ($user->role === 'mentor') {
            $mentorData = [
                'welcome_statement' => $request->welcome_statement,
                'years_of_experience' => (int)$request->years_of_experience,
            ];

            if ($request->filled('skills')) {
                $mentorData['skills'] = $request->skills;
            }

            $mentor = $user->mentor()->create($mentorData);

            if ($request->filled('tracks')) {
                $mentor->tracks()->sync($request->tracks);
            }
        }

        $token = Auth::guard('api')->login($user);

        return $this->successResponse([
            'user' => new UserResource($user->load('mentor.tracks')),
            'token' => $token,
        ], 'User registered successfully.');
    }
}
