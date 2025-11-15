<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
then: function () {
    $modules = [
        'User' => ['path' => 'app/Modules/User/adminRoutes.php', 'prefix' => 'api/admin'],
        'Auth' => ['path' => 'app/Modules/Auth/authRoutes.php', 'prefix' => 'api/'],
        'Author' => ['path' => 'app/Modules/Author/routes/author.php', 'prefix' => 'api/'],
    ];

    foreach ($modules as $module) {
        $file = base_path($module['path']);
        if (file_exists($file)) {
            Route::middleware(['api'])
                ->prefix($module['prefix'])
                ->group($file);
        }
    }
}
    )
    ->withMiddleware(function (Middleware $middleware): array {
        return [
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'isLibrarian' => \App\Http\Middleware\IsLibrarian::class,
        ];
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
