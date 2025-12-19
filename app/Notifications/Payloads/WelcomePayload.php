<?php

namespace App\Notifications\Payloads;

class WelcomePayload
{
    public static function build($deviceToken, $userName)
    {
        return [
            "token" => $deviceToken,
            "notification" => [
                "title" => "Welcome to Our App 🎉",
                "body"  => "Hello {$userName}, we’re excited to have you on board!"
            ],
            "data" => [
                "type" => "welcome",
                "user_name" => $userName
            ]
        ];
    }
}
