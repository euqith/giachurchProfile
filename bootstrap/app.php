<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 🔒 1. DAFTARKAN ALIAS MIDDLEWARE CMS
        $middleware->alias([
            'cms.access' => \App\Http\Middleware\IsCMSAccessible::class,
        ]);

        // 🛡️ 2. ATUR TENDANGAN REDIRECT SAAT USER BELUM LOGIN (GUEST)
        $middleware->redirectGuestsTo(function ($request) {
            // Jika mencoba akses /admin/dashboard dll tanpa login, tendang ke /admin
            if ($request->is('admin/*') && !$request->is('admin')) {
                return route('admin.login');
            }
            
            // Default untuk rute publik lainnya terlempar ke login biasa
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();