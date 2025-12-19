<?php

namespace App\Services;

use App\Services\ApiLogService;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class JsonParserService
{
    /**
     * Convert JSON string to associative array for Cart data.
     *
     * @param string $json
     * @return array|false
     */
    public function parse(string $json)
    {
        try {
            // Log the incoming raw JSON request
            ApiLogService::info('JSON Request Received', ['payload' => $json]);

            // Decode JSON
            $data = json_decode($json, true);

            // Handle decode error
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error = json_last_error_msg();
                ApiLogService::error('JSON Decode Error', ['error' => $error, 'payload' => $json]);
                throw new InvalidArgumentException('Invalid JSON provided: ' . $error);
            }

            // Return valid array
            ApiLogService::info('JSON Parsed Successfully', ['parsed' => $data]);
            return $data;

        } catch (\Exception $e) {
            // Log and return false on failure
            ApiLogService::error('Server Error During JSON Parse', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $json
            ]);
            return false;
        }
    }
}
