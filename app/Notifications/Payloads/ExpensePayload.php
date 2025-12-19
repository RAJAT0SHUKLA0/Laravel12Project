<?php

namespace App\Notifications\Payloads;

class ExpensePayload
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
