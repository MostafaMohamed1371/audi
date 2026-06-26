<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ApiErrorResponse
{
    /**
     * @param  array<string, list<string>>|null  $errors
     * @param  array<string, mixed>|null  $debug
     */
    public static function make(
        int $status,
        string $message,
        ?string $error = null,
        ?array $errors = null,
        ?array $debug = null,
    ): JsonResponse {
        $payload = [
            'status' => $status,
            'message' => $message,
        ];

        if ($error !== null) {
            $payload['error'] = $error;
        }

        if ($errors !== null && $errors !== []) {
            $payload['errors'] = $errors;
        }

        if ($debug !== null && $debug !== []) {
            $payload['debug'] = $debug;
        }

        return response()->json($payload, $status);
    }

    public static function unauthenticated(): JsonResponse
    {
        return self::make(401, 'Unauthenticated.', 'unauthenticated');
    }

    public static function forbidden(?string $message = null): JsonResponse
    {
        return self::make(403, $message ?? 'Forbidden.', 'forbidden');
    }

    public static function notFound(?string $message = null): JsonResponse
    {
        return self::make(404, $message ?? 'Resource not found.', 'not_found');
    }

    public static function methodNotAllowed(): JsonResponse
    {
        return self::make(405, 'Method not allowed.', 'method_not_allowed');
    }

    /**
     * @param  array<string, list<string>>  $errors
     */
    public static function validation(string $message, array $errors): JsonResponse
    {
        return self::make(422, $message, 'validation_error', $errors);
    }

    public static function badRequest(string $message): JsonResponse
    {
        return self::make(400, $message, 'bad_request');
    }

    public static function serverError(?string $message = null, ?array $debug = null): JsonResponse
    {
        return self::make(
            500,
            $message ?? 'Server error.',
            'server_error',
            debug: $debug,
        );
    }

    public static function fromHttpStatus(int $status, string $message): JsonResponse
    {
        $error = match ($status) {
            400 => 'bad_request',
            401 => 'unauthenticated',
            403 => 'forbidden',
            404 => 'not_found',
            405 => 'method_not_allowed',
            409 => 'conflict',
            422 => 'validation_error',
            429 => 'too_many_requests',
            500 => 'server_error',
            503 => 'service_unavailable',
            default => 'http_error',
        };

        return self::make($status, $message, $error);
    }
}
