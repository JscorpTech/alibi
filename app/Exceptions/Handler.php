<?php

namespace App\Exceptions;

use App\Http\Helpers\ExceptionHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception): Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        return $this->shouldReturnJson($request, $exception)
            ? ExceptionHelper::sendError($exception->getMessage())
            : redirect()->guest(route('auth'));
    }
}
