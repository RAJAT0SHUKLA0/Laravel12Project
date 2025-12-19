<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\PersonalAccessToken;
use App\Services\ApiLogService;
use App\Helper\Message;
use App\Models\Token;
use Illuminate\Support\Facades\Validator;



class LoginUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'mobile'   => 'required|numeric|digits:10',
                'password' => 'required|string|min:6',
                'deviceInfo'=>'required'
            ]);
            ApiLogService::info('Login request received', $request->all());
            $user = User::with('role')->where('mobile', $request->mobile)->where('role_id','!=',4)->whereNotIn('status',[0,3])->first();

            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is inactive or blocked.'
                ], 401);
            }
            

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user); 

                $token = $user->createToken('api-token')->plainTextToken;
                Token::create(['token'=>$token,'user_id'=>auth()->id()]);
                $user->remember_token = $token;
                $user->device_info =$request->deviceInfo;
                $user->save();
                ApiLogService::success('User logged in successfully',$user);
                return response()->json([
                    'status' => true,
                    'message' => sprintf(Message::LOGIN_SUCCESS_MESSAGE,$user->name,$user->role->name),
                    'token'   => $token,
                    'user_id' =>(string)$user->id,
                    "role" =>$user->role->name,
                    "name" => $user->name
                    
                ], 200);
            } else {
                ApiLogService::warning('Invalid mobile number or password', $request->all());
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credential',
                ], 401);
            }

        } catch (\Exception $e) {
            ApiLogService::warning('Something went wrong! Please try again.', $e);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong! Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    public function LoginByRider(Request $request)
    {
        try {
            $request->validate([
                'mobile'   => 'required|numeric|digits:10',
                'password' => 'required|string|min:6',
                'deviceInfo'=>'required'
            ]);
            ApiLogService::info('Login request received', $request->all());
            $user = User::with('role')->where('mobile', $request->mobile)->where('role_id',4)->whereNotIn('status',[0,3])->first();

            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is inactive or blocked.'
                ], 401);
            }
            

            if ($user && Hash::check($request->password, $user->password)) { 
                Auth::login($user); 

                $token = $user->createToken('api-token')->plainTextToken;
                Token::create(['token'=>$token,'user_id'=>auth()->id()]);
                $user->remember_token = $token;
                $user->device_info =$request->deviceInfo;
                $user->save();
                ApiLogService::success('User logged in successfully',$user);
                return response()->json([
                    'status' => true,
                    'message' => sprintf(Message::LOGIN_SUCCESS_MESSAGE,$user->name,$user->role->name),
                    'token'   => $token,
                    'user_id' =>(string)$user->id,
                    "role" =>$user->role->name,
                    "name" => $user->name
                    
                ], 200);
            } else {
                ApiLogService::warning('Invalid mobile number or password', $request->all());
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credential',
                ], 401);
            }

        } catch (\Exception $e) {
            ApiLogService::warning('Something went wrong! Please try again.', $e);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong! Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    public function logout(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userId' => 'required|integer|exists:tbl_users,id'
            ]);
            $authHeader = $request->header('Authorization');
            $tokenString = substr($authHeader, 7);
            $userId = $request->input('userId');
            $existToken = Token::where('user_id',$userId)->get();
            foreach($existToken as $token){
                if($tokenString == $token->token){
                     $latestToken = PersonalAccessToken::findToken($token->token);
                     $latestToken->delete();
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'User logged out from all devices successfully.',
            ]);
        } catch (\Exception $e) {
            ApiLogService::warning('Failed to clear tokens for user', $e);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
