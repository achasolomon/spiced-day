<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your custom middleware aliases here
        $middleware->alias([
            'user.type' => \App\Http\Middleware\CheckUserType::class,
            'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
            'application.owner' => \App\Http\Middleware\EnsureApplicationOwner::class,
            'consultant.assigned' => \App\Http\Middleware\EnsureConsultantAssigned::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle AJAX/JSON requests
        $exceptions->render(function (\Throwable $exception, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                if ($exception instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $exception->errors()
                    ], 422);
                }
                
                if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated'
                    ], 401);
                }
                
                if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }
                
                if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found'
                    ], 404);
                }
                
                // Generic error response
                $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
                return response()->json([
                    'success' => false,
                    'message' => $statusCode === 500 ? 'An internal server error occurred. Please try again later.' : 'An error occurred'
                ], $statusCode);
            }
        });
    })->create();