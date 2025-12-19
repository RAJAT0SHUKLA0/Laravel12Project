<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PersonalAccessToken;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Token;

class CheckSanctumToken
{

    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Token not provided','status' => false], 401);
        }
        $tokenString = substr($authHeader, 7);
        $existUser = Token::where('token',$tokenString)->get();
        if(sizeof($existUser) == 0){
          return response()->json(['message' => 'Invalid  token', 'status' => false], 401);    
        }
        foreach($existUser as $userToken){
            $existUser = User::find($userToken->user_id);
            if($existUser->status ==0 ||  $existUser->status ==0 ){
                 return response()->json(['message' => 'Invalid  token', 'status' => false], 401);    
            }
            $token = PersonalAccessToken::findToken($userToken->token);
            if (!$token || !$token->tokenable_id) {
                return response()->json(['message' => 'Invalid or expired token', 'status' => false], 401);
            }
            
            if(!$existUser && $existUser->role_id == 4){
                $tokenUserId = $token->tokenable;
                if (auth()->id() !== $tokenUserId) {
                    return response()->json(['message' => 'Access denied: User mismatch'], 403);
                } 
            }
            $expiryValue = config('constants.TOKEN_EXPIRY_TIME');
            $expiryUnit  = config('constants.TOKEN_EXPIRY_UNIT');
            $tokenCreated = $token->created_at;
            $tokenExpiry  = $tokenCreated->copy()->add($expiryUnit, $expiryValue);
            $token->update(['expires_at'=>$tokenExpiry]);
            if (now()->gt($tokenExpiry)) {
                $token->delete();
                return response()->json(['message' => 'Token expired', 'status' => false], 401);
            }
    
            auth()->setUser($token->tokenable);
            return $next($request);
        }
    }
}
