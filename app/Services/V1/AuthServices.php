<?php

namespace App\Services\V1;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
    public function store(array $data){
         $user =    User::create($data);
         try {
            $this->otpServices->sendOtp($user);
            Log::info('OTP email sent successfully: ');
        } catch (\Throwable $e) {
            Log::error('OTP email failed: ' . $e->getMessage());
        }
        $token = $user->createToken('bearer_token')->plainTextToken;
        return [
            'token' => $token,
            'message' => 'Registration Successful. Otp sent to '. $user->email ,
        ];

    }

    public function login(array $credentials){
      if (!Auth::attempt($credentials))  {
        return ['error' => 'Invalid credentials'];
      }
      $user = Auth::user();
      $user->tokens()->delete();
     $token = $user->createToken('Bearer Token')->plainTextToken;
     return $token;
    }

    public function verifyEmail($otp){
        $user = Auth::user();

        if (!$this->otpServices->verifyOtp($user, $otp)){
            return [ 'error' => 'invalid or expired otp'];
        };
        $this->otpServices->clearOtp($user);
  
        $user->email_verified_at = Carbon::now();

        $user->save();
        return true ;
    }

    public function resendOtp(){
        $this->otpServices->sendOtp(Auth::user()) ;
    }

 public function completeProfile(array $data)
{
    $user = Auth::user();
    if (isset($data['profile_picture'])) {
        $data['profile_picture'] = $data['profile_picture']->store('profile_pictures', 'public');
    }  
    $user->update($data);
    return [
        'success' => true,
        'message' => 'Profile completed successfully'
    ];
}

public function updateProfile(array $validated)
{
    $user = Auth::user();
    if (isset($validated['profile_picture'])) {
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        $validated['profile_picture'] = $validated['profile_picture']->store('profile_pictures', 'public');
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
        'message' => 'Profile updated successfully'
    ];
}

    
    public function changePassword($validated){
        $user = Auth::user();
        if (!Hash::check($validated['password'], $user->password)){
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

    public function forgotPassword($validated) {
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
   
    public function resetPassword($validated) {
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
public function getProfile(){
    return Auth::user();
}

   public function logout(){
    Auth::user()->currentAccessToken()->delete();
}

}
