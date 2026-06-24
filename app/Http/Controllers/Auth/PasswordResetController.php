<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ForgotPasswordRequest;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Services\Otp\OtpService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    use ApiResponse;

    public function __construct(private OtpService $otpService) {}

    public function sendOtp(ForgotPasswordRequest $request): JsonResponse
    {
        $error = $this->otpService->sendPasswordResetOtp($request->validated('email'));

        if ($error) {
            return $this->error(
                $error['message'],
                $error['code'],
                $error['errors'] ?? [],
            );
        }

        return $this->success(
            null,
            'If an account exists for this email, an OTP has been sent.',
        );
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $error = $this->otpService->resetPassword(
            $request->validated('email'),
            $request->validated('otp'),
            $request->validated('password'),
        );

        if ($error) {
            return $this->error(
                $error['message'],
                $error['code'],
                $error['errors'] ?? [],
            );
        }

        return $this->success(null, 'Password reset successfully.');
    }
}
