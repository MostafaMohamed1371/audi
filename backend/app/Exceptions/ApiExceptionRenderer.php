<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ApiErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class ApiExceptionRenderer
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (ValidationException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiErrorResponse::validation(
                $e->getMessage() ?: 'The given data was invalid.',
                $e->errors(),
            );
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiErrorResponse::unauthenticated();
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiErrorResponse::forbidden($e->getMessage() ?: null);
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiErrorResponse::notFound('Resource not found.');
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $raw = $e->getMessage();
            $message = (str_contains($raw, 'could not be found') || str_contains($raw, 'Route'))
                ? 'Endpoint not found.'
                : 'Resource not found.';

            return ApiErrorResponse::notFound($message);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiErrorResponse::methodNotAllowed();
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($e instanceof NotFoundHttpException || $e instanceof MethodNotAllowedHttpException) {
                return null;
            }

            return ApiErrorResponse::fromHttpStatus(
                $e->getStatusCode(),
                $e->getMessage() ?: 'Request failed.',
            );
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($e instanceof ValidationException
                || $e instanceof AuthenticationException
                || $e instanceof AuthorizationException
                || $e instanceof ModelNotFoundException
                || $e instanceof NotFoundHttpException
                || $e instanceof MethodNotAllowedHttpException
                || $e instanceof HttpException
            ) {
                return null;
            }

            $debug = config('app.debug')
                ? [
                    'exception' => $e::class,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
                : null;

            return ApiErrorResponse::serverError('Server error.', $debug);
        });
    }
}
