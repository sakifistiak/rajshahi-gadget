<?php

use App\Http\Middleware\CachePublicJson;
use App\Http\Middleware\EnforceCanonicalUrl;
use App\Http\Middleware\EnsureCartToken;
use App\Http\Middleware\EnsureChatAccess;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\NoStore;
use App\Http\Middleware\TrackVisitorAnalytics;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(EnforceCanonicalUrl::class);
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'chat.access' => EnsureChatAccess::class,
            'public.json' => CachePublicJson::class,
            'no.store' => NoStore::class,
        ]);
        // no.store must wrap route-model binding, so a 404 for an unknown order number is never stored either.
        $middleware->prependToPriorityList(before: SubstituteBindings::class, prepend: NoStore::class);
        $middleware->web(append: [
            EnsureCartToken::class,
            TrackVisitorAnalytics::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
