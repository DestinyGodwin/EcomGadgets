<?php

namespace App\Http\Controllers\V1\Auth;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;
use App\Services\V1\AuthServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\LogoutRequest;
use App\Http\Resources\V1\Auth\UserResource;
use App\Http\Requests\V1\Auth\VerifyEmailRequest;
use App\Http\Requests\V1\Auth\RegisterUserRequest;
use App\Http\Requests\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\V1\Auth\UpdateProfileRequest;
use App\Http\Requests\V1\Auth\ChangePasswordRequest;
use App\Http\Requests\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\V1\Auth\CompleteProfileRequest;

class AuthController extends Controller
{

    public function __construct(protected AuthServices $authServices) {}

    public function store(RegisterUserRequest $request)
    {
        $data = $this->authServices->store($request->validated());

        return response()->json([
            'message'   => $data['message'],
            'token'     => $data['token'],
            'role_code' => $data['role_code'],
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $device = $request->only(['device_token',  'device_name']);

        $data = $this->authServices->login($credentials, $device);

        if (isset($data['error'])) {
            return response()->json(['message' => $data['error']], 401);
        }

        return response()->json([
            'message'   => 'Logged in successfully',
            'token'     => $data['token'],
            'role_code' => $data['role_code'],
        ]);
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        $data = $this->authServices->verifyEmail($request->otp);
        if (isset($data['error'])) {
            return response()->json(['message' => $data['error']], 401);
        }
        return response()->json(['message' => 'Email verified successfully']);
    }

    public function resendOtp()
    {
        $this->authServices->resendOtp();
        return response()->json('Otp sent');
    }

    public function completeProfile(CompleteProfileRequest $request)
    {
        $user = $this->authServices->completeProfile($request->validated());
        return response()->json($user);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->authServices->updateProfile($request->validated());
        return response()->json($user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $this->authServices->changePassword($request->validated());
        if (! $data['success']) {
            return response()->json(['message' => $data['message']], 422);
        }
        return response()->json(['message' => $data['message']], 200);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $this->authServices->forgotPassword($request->validated());
        return response()->json($data);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $this->authServices->resetPassword($request->validated());
        if (! $data['success']) {
            return response()->json(['message' => $data['message']], 422);
        }
        return response()->json(['message' => $data['message']], 200);
    }

    public function getProfile()
    {
        $user = $this->authServices->getProfile();
        return new UserResource($user);
    }

    public function logout(LogoutRequest $request)
    {
        $this->authServices->logout($request->device_token);
        return response()->json('Logged out successfully');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $data = $this->authServices->googleLoginOrRegister($googleUser);

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $data['token'],
            'role_code' => $data['role_code'],
        ]);
    }

    public function googleMobileLogin(Request $request)
    {
        $request->validate(['id_token' => 'required|string']);

        $payload = json_decode(
            file_get_contents('https://oauth2.googleapis.com/tokeninfo?id_token=' . $request->id_token),
            true
        );

        if (!isset($payload['email'])) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $data = $this->authServices->googleLoginOrRegister((object)[
            'id' => $payload['sub'],
            'email' => $payload['email'],
            'name' => $payload['name'] ?? null,
        ]);

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $data['token'],
            'role_code' => $data['role_code'],
        ]);
    }
}
