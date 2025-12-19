<?php

namespace App\Notifications\Payloads;

class NewSellerAddPayload
{
    public static function build($deviceToken, $title, $body)
    {
        return [
            "token" => $deviceToken,
            "data" => [
                "title" => $title,
                "body"  => $body,
                "route" => "register_staff_member"
            ]
        ];
    }
}
