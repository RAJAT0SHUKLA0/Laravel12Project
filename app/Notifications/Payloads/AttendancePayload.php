<?php

namespace App\Notifications\Payloads;

class AttendancePayload
{
    public static function build($deviceToken, $staffName)
    {
        return [
            "token" => $deviceToken,
            "notification" => [
                "title" => "Attendance Marked",
                "body" => "{$staffName} has marked attendance."
            ],
            "data" => [
                "type" => "attendance",
                "staff_name" => $staffName
            ]
        ];
    }
}
