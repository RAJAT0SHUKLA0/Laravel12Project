<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && in_array($user->status, [0, 3])) {
            $rawToken = $request->bearerToken(); 
            if ($rawToken) {
                $tokenId = null;
                $tokenValue = $rawToken;
    
                if (str_contains($rawToken, '|')) {
                    [$tokenId, $plainToken] = explode('|', $rawToken, 2);
                    $hashedToken = hash('sha256', $plainToken);
    
                    $personalAccessToken = $user->tokens()
                        ->where('id', $tokenId)
                        ->where('token', $hashedToken)
                        ->first();
                } else {
                    $personalAccessToken = $user->tokens()
                        ->where('token', $tokenValue)
                        ->first();
                }
                if ($personalAccessToken) {
                    $personalAccessToken->delete(); 
                }
            }
        }
    
        return $next($request);
    }

}


