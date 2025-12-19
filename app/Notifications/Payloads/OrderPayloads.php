<?php

namespace App\Notifications\Payloads;

class OrderPayloads
{
    public static function createOrder($deviceToken, $title, $body,$image,$route)
    {
        return [
            "token" => $deviceToken,
            "data" => [
                "title" => $title,
                "body"  => $body,
                "route" => $route,
                "image" => $image
            ]
        ];
    }
    
    public static function orderAssign($deviceToken, $title, $body,$route)
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
    
    public static function orderPickup($deviceToken, $title, $body,$route)
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
    
     public static function orderOutForDelivery($deviceToken, $title, $body,$route)
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
    
    public static function orderdelivery($deviceToken, $title, $body,$route)
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
