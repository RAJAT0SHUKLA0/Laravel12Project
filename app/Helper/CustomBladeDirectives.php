<?php
namespace App\Helper;

use Illuminate\Support\Facades\Blade;

class CustomBladeDirectives
{
    public static function register()
    {
        Blade::directive('encryptedRoute', function ($expression) {
            return "<?php \$__args = [{$expression}]; echo route(\$__args[0], [\\App\\Utils\\Crypto::encryptId(\$__args[1]), ...array_slice(\$__args, 2)]); ?>";
        });
    }
}