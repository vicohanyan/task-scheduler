<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * convert exceptions to json format for API
 */
final class ApiExceptionRegistrar
{
    public static function register(Exceptions $exceptions): void
    {
        $isApi = fn (Request $r) => $r->is('api/*');

        $exceptions->render(function (ValidationException $e, Request $r) use ($isApi) {
            if ($isApi($r)) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $r) use ($isApi) {
            if ($isApi($r)) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $r) use ($isApi) {
            if ($isApi($r)) {
                return response()->json(['message' => 'Not found'], 404);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $r) use ($isApi) {
            if ($isApi($r)) {
                return response()->json(
                    ['message' => $e->getMessage() ?: 'Error'],
                    $e->getStatusCode()
                );
            }
        });

        $exceptions->render(function (Throwable $e, Request $r) use ($isApi) {
            if ($isApi($r)) {
                return response()->json(['message' => 'Server Error'], 500);
            }
        });
    }
}
