<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'El token ha expirado'], 422);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'El token es inválido'], 422);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                return response()->json(['error' => 'El token está ausente'], 422);
            }
        });
    }
}
