<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Force HTTPS untuk semua URL & Asset
        if (config('app.env') === 'production' || request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            URL::forceScheme('https');
        }

        // 2. Potong Subfolder fisik dari request path Symfony/Laravel
        $baseUrl = config('app.url');
        URL::forceRootUrl($baseUrl);

        // Paksa Symfony Request untuk tidak membaca prefix /giaswebsite/public
        request()->server->set('SCRIPT_NAME', '/index.php');
        request()->server->set('SCRIPT_FILENAME', '/index.php');
    }
}