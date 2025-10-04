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
        //
    })->create();