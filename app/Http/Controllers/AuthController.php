<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $accessCode = Str::random(32);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'access_code' => $accessCode,
            'status' => 0,
        ]);

        // Send verification email
        $this->sendVerificationEmail($user, $accessCode);

        return response()->json([
            'success' => true,
            'message' => 'User was created & verification link was sent to your email.',
            'data' => $user->id
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found'
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Account not active yet. Please verify your account.'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password is wrong'
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Successful login.',
            'jwt' => $token,
            'data' => [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email
            ]
        ]);
    }

    public function verify(Request $request)
    {
        $accessCode = $request->query('access_code');
        
        $user = User::where('access_code', $accessCode)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Access code not found.'
            ], 404);
        }

        $user->update(['status' => 1]);

        return redirect(config('app.url') . '/auth/welcome.html');
    }

    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('access_code', $request->access_code)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Your access code was not found.'
            ]);
        }

        $user->update(['status' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Your account has been verified.'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email is not registered yet.'
            ]);
        }

        $accessCode = Str::random(32);
        $user->update(['access_code' => $accessCode]);

        // Send reset password email
        $this->sendResetPasswordEmail($user, $accessCode);

        return response()->json([
            'success' => true,
            'message' => 'Reset link already sent to your email.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_code' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('access_code', $request->access_code)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please insert your email first.'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'access_code' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset.'
        ]);
    }

    public function me()
    {
        $user = auth()->user();
        
        return response()->json([
            'success' => true,
            'payload' => [
                'id' => $user->id,
                'email' => $user->email,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'avatar' => $user->avatar,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'address' => $user->address,
                'business' => $user->vendor ? $user->vendor->id : null,
            ],
            'message' => 'Got Data Successfully'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'bio' => 'sometimes|string',
            'address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user->update($request->only([
            'firstname', 'lastname', 'email', 'phone', 'bio', 'address'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'User profile was updated.'
        ]);
    }

    private function sendVerificationEmail($user, $accessCode)
    {
        $link = config('app.url') . "/api/auth/verify?access_code={$accessCode}";
        
        // Email sending logic here
        // You can use Laravel's Mail facade or implement your preferred email service
    }

    private function sendResetPasswordEmail($user, $accessCode)
    {
        $link = config('app.url') . "/account/reset?access_code={$accessCode}";
        
        // Email sending logic here
    }
}