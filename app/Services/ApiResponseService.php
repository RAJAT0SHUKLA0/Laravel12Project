<?php
namespace App\Services;

class ApiResponseService
{
     public static function success(string $message = 'Success', $data = null)
    {
        // Initialize base response
        $response = [
            'status'  => true,
            'message' => $message,
        ];

        // Handle paginated data
        if ($data instanceof LengthAwarePaginator || $data instanceof Paginator) {
            $response['data'] = $data->items(); // actual data list
            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ];
        } else {
            $response['data'] = $data ?? (object)[];
        }

        return response()->json($response, 200);
    }
        public static function info(string $message = 'Info', $data = null, int $code = 200)
        {
            return response()->json([
                'status'  => 'info',
                'message' => $message,
                'data'    => $data ?? (object)[],
            ], $code);
        }

    public static function error(string $message = 'An error occurred', $error = null, int $code = 400)
    {
        return response()->json([
            'status'  => false,
            'message' =>  $error,
            'data'    => (object)[]
        ], $code);
    }

    public static function validation(string $message = 'Validation failed', $error = [], int $code = 422)
    {
        return response()->json([
            'status'  => false,
            'message' => $error,
            'data'    => (object)[]
        ], $code);
    }
}


?>