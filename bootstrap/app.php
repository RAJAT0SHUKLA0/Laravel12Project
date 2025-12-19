<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\admin\AuthOrGuestAdminMiddleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(["auth.or.guest.admin"=>AuthOrGuestAdminMiddleware::class,'abilities' => CheckAbilities::class,
        'ability' => CheckForAnyAbility::class,'check.sanctum' => \App\Http\Middleware\CheckSanctumToken::class,'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,'rate.limit' => \App\Http\Middleware\RateLimiterMiddleware::class,'reject.query.string'=>\App\Http\Middleware\RejectQueryStringInputs::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
