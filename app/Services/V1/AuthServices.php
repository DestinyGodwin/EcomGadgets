<?php

namespace App\Services\V1;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\V1\Auth\UserResource;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthServices
{
    /**
     * Create a new class instance.
     */
    protected $otpService;
    public function __construct(protected OtpServices $otpServices)
    {
        $this->otpService = $otpServices;
    }

    protected const ROLE_CODES = [
        'admin' => 'A1Z9X',
        'vendor' => 'V3D7Q',
        'customer' => 'C5B2L',
    ];
    public function store(array $data)
    {
        $deviceData = [
            'device_token' => $data['device_token'] ?? null,
            'device_name' => $data['device_name'] ?? null,
            'platform' => $data['platform'] ?? null,
        ];

        unset($data['device_token'], $data['device_name'], $data['platform']);
        $user =    User::create($data);
        // Register device if provided
        if (! empty($deviceData['device_token'])) {
            $this->registerDevice($user, $deviceData);
        }
        try {
            $this->otpServices->sendOtp($user);
            Log::info('OTP email sent successfully: ');
        } catch (\Throwable $e) {
            Log::error('OTP email failed: ' . $e->getMessage());
        }
        $token = $user->createToken('bearer_token')->plainTextToken;
        return [
            'token' => $token,
            'role_code' => self::ROLE_CODES[$user->role] ?? null,
            'message' => 'Registration Successful. Otp sent to ' . $user->email,
        ];
    }

    public function login(array $credentials,  array $device = [])
    {
        if (!Auth::attempt($credentials)) {
            return ['error' => 'Invalid credentials'];
        }
        $user = Auth::user();
        $user->tokens()->delete();

        if (! empty($device['device_token'])) {
            $this->registerDevice($user, $device);
        }
        $token = $user->createToken('Bearer Token')->plainTextToken;
        return [
            'token' => $token,
            'role_code' => self::ROLE_CODES[$user->role] ?? null,
        ];
    }

    public function verifyEmail($otp)
    {
        $user = Auth::user();

        if (!$this->otpServices->verifyOtp($user, $otp)) {
            return ['error' => 'invalid or expired otp'];
        };
        $this->otpServices->clearOtp($user);

        $user->email_verified_at = Carbon::now();

        $user->save();
        return true;
    }

    public function resendOtp()
    {
        $this->otpServices->sendOtp(Auth::user());
    }

    // public function completeProfile(array $data)


    // {
    //     $user = Auth::user();
    //     if (isset($data['profile_picture'])) {
    //         $data['profile_picture'] = $data['profile_picture']->store('profile_pictures', 'public');
    //     }
    //     $user->update($data);
    //     return [
    //         'success' => true,
    //         'user' => new UserResource($user),
    //         'message' => 'Profile completed successfully'
    //     ];
    // }

    public function completeProfile(array $data)
    {
        $user = Auth::user();

        if (isset($data['profile_picture'])) {
            $user->clearMediaCollection('profile_picture');

            $user->addMedia($data['profile_picture'])
                ->toMediaCollection('profile_picture');

            unset($data['profile_picture']);
        }

        $user->update($data);

        return [
            'success' => true,
            'user' => new UserResource($user),
            'message' => 'Profile completed successfully',
        ];
    }

    // public function updateProfile(array $validated)
    // {
    //     $user = Auth::user();
    //     if (isset($validated['profile_picture'])) {
    //         if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
    //             Storage::disk('public')->delete($user->profile_picture);
    //         }
    //         $validated['profile_picture'] = $validated['profile_picture']->store('profile_pictures', 'public');
    //     }

    //     $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

    //     $user->fill($validated);

    //     if ($emailChanged) {
    //         $user->email_verified_at = null;
    //         $this->otpServices->sendOtp($user);
    //     }
    //     $user->save();
    //     return [
    //         'success' => true,
    //         'user' => new UserResource($user),
    //         'message' => 'Profile updated successfully'
    //     ];
    // }



    public function updateProfile(array $validated)
    {
        $user = Auth::user();

        if (isset($validated['profile_picture'])) {
            $user->clearMediaCollection('profile_picture');

            $user->addMedia($validated['profile_picture'])
                ->toMediaCollection('profile_picture');

            unset($validated['profile_picture']);
        }

        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
            $this->otpServices->sendOtp($user);
        }

        $user->save();

        return [
            'success' => true,
            'user' => new UserResource($user),
            'message' => 'Profile updated successfully',
        ];
    }

    public function changePassword($validated)
    {
        $user = Auth::user();
        if (!Hash::check($validated['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'invalid old password'
            ];
        }
        $user->password = Hash::make($validated['new_password']);
        $user->save();
        return [
            'success' => true,
            'message' => 'Password changed successfully'
        ];
    }

    public function forgotPassword($validated)
    {
        try {
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                $this->otpServices->sendOtp($user);
            }
            return [
                'success' => true,
                'message' => 'You will receive an OTP to complete resetting your password if you have a record with us.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong while processing your request.'
            ];
        }
    }

    public function resetPassword($validated)
    {
        try {
            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired OTP.'
                ];
            }
            $verified = $this->otpServices->verifyOtp($user, $validated['otp']);
            if (!$verified) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired OTP.'
                ];
            }

            $user->password = Hash::make($validated['new_password']);
            $this->otpServices->clearOtp($user);
            $user->save();
            return [
                'success' => true,
                'message' => 'Password reset successfully.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong while processing your request.'
            ];
        }
    }
    public function getProfile()
    {
        return Auth::user();
    }

    //    public function logout(){
    //     Auth::user()->currentAccessToken()->delete();


    public function logout(?string $deviceToken = null): void
    {
        $user = Auth::user();

        if ($deviceToken) {
            UserDevice::where('user_id', $user->id)
                ->where('device_token', $deviceToken)
                ->delete();
        }

        $user->currentAccessToken()->delete();
    }

    /**
     * Register or update device token (single source of truth)
     *
     * @param User  $user
     * @param array $data
     */
    protected function registerDevice(User $user, array $data): void
    {
        $deviceToken = $data['device_token'] ?? null;
        if (! $deviceToken) {
            return;
        }

        UserDevice::where('device_token', $deviceToken)
            ->where('user_id', '!=', $user->id)
            ->delete();

        UserDevice::updateOrCreate(
            ['device_token' => $deviceToken],
            [
                'user_id' => $user->id,
                'platform' => $data['platform'] ?? null,
                'device_name' => $data['device_name'] ?? null,
            ]
        );
    }

    public function googleLoginOrRegister(SocialiteUser $googleUser)
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'password' => "",
            ]);
        }

        if (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('Bearer Token')->plainTextToken;

        return [
            'token' => $token,
            'role_code' => self::ROLE_CODES[$user->role] ?? null,
        ];
    }

    public function requestDeleteAccount(): void
    {
        $user = Auth::user();

        $this->otpServices->sendOtp($user);
    }

    public function confirmDeleteAccount(string $otp): array
    {
        $user = Auth::user();

        if (! $this->otpServices->verifyOtp($user, $otp)) {
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ];
        }

        $this->otpServices->clearOtp($user);

        $user->tokens()->delete();
        UserDevice::where('user_id', $user->id)->delete();

        if (method_exists($user, 'clearMediaCollection')) {
            $user->clearMediaCollection('profile_picture');
        }

        Auth::logout();
        $user->delete();

        return [
            'success' => true,
            'message' => 'Account deleted successfully.',
        ];
    }
}