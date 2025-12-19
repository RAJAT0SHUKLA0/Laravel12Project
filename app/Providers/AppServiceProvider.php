<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\RepositoryServiceProvider;

use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
