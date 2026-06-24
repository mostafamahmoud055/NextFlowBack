<?php

namespace App\Services\Otp;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generateAndSave(User $user): string
    {
        $otp = $this->generateOtp();

        $user->forceFill([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(config('otp.expiry_minutes')),
        ])->save();

        return $otp;
    }

    public function send(User $user, string $otp, string $purpose): ?array
    {
        if (config('otp.dev_mode')) {
            return null;
        }

        Mail::to($user->email)->send(new OtpMail($otp, $purpose));

        return null;
    }

    public function verify(User $user, string $otp): bool
    {
        if ($this->isDevOtp($otp)) {
            return true;
        }

        if (! $user->otp || ! $user->otp_expires_at) {
            return false;
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return false;
        }

        return hash_equals($user->otp, $otp);
    }

    public function clear(User $user): void
    {
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);
    }

    public function sendEmailVerificationOtp(User $user): ?array
    {
        $user = $user->fresh();

        if ($user->email_verified_at) {
            return ['message' => 'Email is already verified.', 'code' => 400];
        }

        $otp = $this->generateAndSave($user);

        if ($error = $this->send($user, $otp, 'email_verification')) {
            return $error;
        }

        return null;
    }

    public function verifyEmailOtp(User $user, string $otp): ?array
    {
        $user = $user->fresh();

        if ($user->email_verified_at) {
            return ['message' => 'Email is already verified.', 'code' => 400];
        }

        if (! $this->isDevOtp($otp) && (! $user->otp || ! $user->otp_expires_at)) {
            return ['message' => 'No OTP found. Please request a new one.', 'code' => 400];
        }

        if (! $this->isDevOtp($otp) && now()->isAfter($user->otp_expires_at)) {
            $this->clear($user);

            return ['message' => 'OTP has expired. Please request a new one.', 'code' => 400];
        }

        if (! $this->verify($user, $otp)) {
            return ['message' => 'Invalid OTP.', 'code' => 400, 'errors' => ['otp' => ['Invalid OTP.']]];
        }

        $user->update(['email_verified_at' => now()]);
        $this->clear($user);

        return null;
    }

    public function sendPasswordResetOtp(string $email): ?array
    {
        $user = User::whereEmail($email)->first();

        if (! $user) {
            return ['message' => 'Invalid email.', 'code' => 400, 'errors' => ['email' => 'Invalid email.']];
        }

        $otp = $this->generateAndSave($user);

        if ($error = $this->send($user, $otp, 'password_reset')) {
            return $error;
        }

        return null;
    }

    public function resetPassword(string $email, string $otp, string $password): ?array
    {
        $user = User::whereEmail($email)->first();

        if (! $user) {
            return ['message' => 'Invalid email.', 'code' => 400, 'errors' => ['email' => 'Invalid email.']];
        }

        if (! $this->isDevOtp($otp) && (! $user->otp || ! $user->otp_expires_at)) {
            return ['message' => 'No OTP found. Please request a new one.', 'code' => 400, 'errors' => ['otp' => 'No OTP found. Please request a new one.']];
        }

        if (! $this->isDevOtp($otp) && now()->isAfter($user->otp_expires_at)) {
            $this->clear($user);

            return ['message' => 'OTP has expired. Please request a new one.', 'code' => 400];
        }

        if (! $this->verify($user, $otp)) {
            return ['message' => 'Invalid OTP.', 'code' => 400, 'errors' => ['otp' => ['Invalid OTP.']]];
        }

        $user->update(['password' => $password]);
        $this->clear($user);

        return null;
    }


    private function generateOtp(): string
    {
        if (config('otp.dev_mode')) {
            return config('otp.dev_code');
        }

        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function isDevOtp(string $otp): bool
    {
        return config('otp.dev_mode') && hash_equals(config('otp.dev_code'), $otp);
    }
}
