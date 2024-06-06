<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'Hospital_manager'=>\App\Http\Middleware\hospital_manager::class,
            'Employee_reseption'=>\App\Http\Middleware\reseption_employee::class,
            'Doctor'=>\App\Http\Middleware\Doctor::class,
            'cors'=>\App\Http\Middleware\CorsMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
