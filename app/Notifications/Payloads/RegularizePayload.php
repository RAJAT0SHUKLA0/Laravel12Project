<?php

namespace App\Notifications\Payloads;

class RegularizePayload
{
    public static function build($deviceToken, $title, $body,$route)
    {
        return [
            "token" => $deviceToken,
            "data" => [
                "title" => $title,
                "body"  => $body,
                "route" => $route
            ]
        ];
    }
}
