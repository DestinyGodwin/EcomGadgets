<?php

namespace App\Services\V1;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\V1\Auth\SendOtpNotification;

class OtpServices
{
    /**
     * Create a new class instance.
     */
     public function generateOtp () {
        return rand(100000, 999999);
    }

    public function sendOtp(User $user){
        $otp = $this->generateOtp();
        $otp_expires_at = Carbon::now()->addMinutes(10);

        $user->otp = Hash::make($otp);
        $user->otp_expires_at = $otp_expires_at;
        $user->save();

        $user->notify(new SendOtpNotification($otp));
    }

    public function verifyOtp(User $user,  $otp){
        return Hash::check($otp,  $user->otp) && Carbon::now()->lessThan($user->otp_expires_at);
    }

    public function clearOtp (User $user){
        $user->otp = null ;
        $user->otp_expires_at = null ;
        $user->save();
    }
}
