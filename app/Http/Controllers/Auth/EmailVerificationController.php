<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Email\VerifyEmailOtpRequest;
use App\Services\Otp\OtpService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    use ApiResponse;

    public function __construct(private OtpService $otpService) {}

    public function sendOtp(): JsonResponse
    {
        $error = $this->otpService->sendEmailVerificationOtp(Auth::user());

        if ($error) {
            return $this->error($error['message'], $error['code']);
        }

        return $this->success(
            null,
            'OTP sent to your email.',
        );
    }

    public function verifyOtp(VerifyEmailOtpRequest $request): JsonResponse
    {
        $error = $this->otpService->verifyEmailOtp(
            Auth::user(),
            $request->validated('otp'),
        );

        if ($error) {
            return $this->error(
                $error['message'],
                $error['code'],
                $error['errors'] ?? [],
            );
        }

        return $this->success(null, 'Email verified successfully.');
    }
}
