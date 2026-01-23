<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    
    // Register the exception handling callbacks for the application.
     
    public function register(): void
    {
        $this->renderable(function (HttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Action not allowed',
                    'status_code' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
        });
    }
}
