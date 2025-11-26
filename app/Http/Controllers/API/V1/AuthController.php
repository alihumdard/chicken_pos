<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user'
            ]);
            return $this->successResponse(null, 'User registered successfully!', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed. Please try again.', 500, $e);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided credentials are not correct.', 401);
            }

            $data = [
                'user' => $user,
                'token' => $user->createToken('auth-token')->plainTextToken,
            ];

            return $this->successResponse($data, 'User successfully logged in.');
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed. Please try again.', 500, $e);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Successfully logged out.');
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed. Please try again.', 500, $e);
        }
    }
}