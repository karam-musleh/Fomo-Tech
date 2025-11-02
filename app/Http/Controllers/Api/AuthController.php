<?php

namespace App\Http\Controllers\Api;


use App\Models\User;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiResponserTrait;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponserTrait;

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('User not found', 401);
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid password', 400);
        }

        $token = Auth::guard('api')->login($user);
        return $this->successResponse(
            [
                new UserResource($user),
                'token' => $token
            ],
            'User logged in successfully',
            200,

        );
    }


    public function logout()
    {
        try {
            auth()->guard('api')->logout();
            return $this->successResponse(null, 'User logged out successfully', 200);
        } catch (JWTException $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            return response()->json([
                'status' => 'success',
                'token' => $newToken
            ], 200);
        } catch (JWTException $e) {
            return $this->errorResponse('Could not refresh token', 401);
        }
    }
}
