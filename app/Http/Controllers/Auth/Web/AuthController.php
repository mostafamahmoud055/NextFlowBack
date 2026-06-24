<?php

namespace App\Http\Controllers\Auth\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
        );

        return $this->withCsrfToken(
            $this->success(new UserResource($user), 'Registered successfully.', 201)
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        if (is_array($user)) {
            return $this->error($user['message'], $user['code'], $user['errors']);
        }

        return $this->withCsrfToken(
            $this->success(new UserResource($user), 'Logged in successfully.')
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->withCsrfToken(
            $this->success(null, 'Logged out successfully.')
        );
    }

    public function me(): JsonResponse
    {
        return $this->success(new UserResource($this->authService->me()));
    }
}
