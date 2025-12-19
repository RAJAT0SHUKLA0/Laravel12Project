<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\ApiLogService;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // You can add custom logging here if needed
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            // Log the error
            ApiLogService::error('Unhandled exception', $exception);

            $status = 500;
            $message = 'Internal Server Error';

            if ($exception instanceof ValidationException) {
                $status = 422;
                $message = $exception->getMessage();
            } elseif ($exception instanceof AuthenticationException) {
                $status = 401;
                $message = 'Unauthenticated';
            } elseif ($exception instanceof ModelNotFoundException) {
                $status = 404;
                $message = 'Resource not found';
            } elseif ($exception instanceof QueryException) {
                $status = 500;
                $message = 'Database error';
            } elseif ($exception instanceof HttpException) {
                $status = $exception->getStatusCode();
                $message = $exception->getMessage() ?: $message;
            }

            return response()->json([
                'status' => false,
                'message' => $message,
                'error' => config('app.debug') ? $exception->getMessage() : null
            ], $status);
        }

        return parent::render($request, $exception);
    }
}
