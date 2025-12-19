<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RejectQueryStringInputs
{
    public function handle(Request $request, Closure $next)
    {
        // If request has any query string parameters, reject it
        if (count($request->query()) > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Parameters must be sent via form-data. Query strings are not allowed.',
            ], 400);
        }

        return $next($request);
    }
}
