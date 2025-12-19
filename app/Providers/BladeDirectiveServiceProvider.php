<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helper\CustomBladeDirectives;

class BladeDirectiveServiceProvider extends ServiceProvider
{
    public function boot()
    {

        CustomBladeDirectives::register();
    }

    public function register()
    {
        //
    }
}
