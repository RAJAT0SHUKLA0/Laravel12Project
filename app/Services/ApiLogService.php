<?php 
namespace App\Services;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Auth;

class ApiLogService
{
    public static function log(string $type, string $message, $data = null, $endpoint = null)
    {
        $userId = Auth::id();
        $ip = request()->ip();
        $path = $endpoint ?? request()->path();

        // Default structured log
        $logData = [
            'log_type'  => $type,
            'message'   => $message,
            'data'      => null,
            'endpoint'  => $path,
            'user_ip'   => $ip,
            'user_id'   => $userId,
            'device_info' => 'null',
        ];

        // If it's an exception, log file and line info
        if ($data instanceof \Throwable) {
            $logData['data'] = json_encode([
                'message' => $data->getMessage(),
                'line'    => $data->getLine(),
                'file'    => $data->getFile(),
                'trace'   => $data->getTraceAsString()
            ]);
        } elseif (is_array($data) || is_object($data)) {
            // Convert array/object data to JSON
            $logData['data'] = json_encode($data);
           
        } else {
            $logData['data'] = $data;
        }
        return ApiLog::create($logData);
    }

    public static function info(string $message, $data = null, $endpoint = null)
    {
        return self::log('info', $message, $data, $endpoint);
    }

    public static function success(string $message, $data = null, $endpoint = null)
    {
        return self::log('success', $message, $data, $endpoint);
    }

    public static function error(string $message, $data = null, $endpoint = null)
    {
        return self::log('error', $message, $data, $endpoint);
    }

    public static function warning(string $message, $data = null, $endpoint = null)
    {
        return self::log('warning', $message, $data, $endpoint);
    }
}
