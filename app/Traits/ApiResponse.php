<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    public function success($data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        if ($data instanceof JsonResource) {
            $data = $data->resolve();
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data ?? [],
            'errors' => [],
        ], $status);
    }

    public function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => [],
            'errors' => $errors,
        ], $status);
    }

    protected function withCsrfToken(JsonResponse $response): JsonResponse
    {
        return $response->header('X-CSRF-TOKEN', csrf_token());
    }
}
