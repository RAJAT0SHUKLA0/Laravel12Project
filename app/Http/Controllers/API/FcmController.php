<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use App\Helper\Message;
use App\Services\FcmService;
use App\Notifications\Payloads\AttendancePayload;
use App\Notifications\Payloads\WelcomePayload;
use App\Notifications\Payloads\ExpensePayload;

class FcmController extends Controller
{
     protected $fcm;
     
     public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }
     
    public function sendWelcomeMessage(Request $request)
    {
        $user = User::find($request->userId);
            if (!$user || !$user->device_id) {
                return response()->json(['status'=> false , 'msg' => 'User not found or device ID missing'], 200);
            }   
        $payload =WelcomePayload::build($user->device_id, $user->name);
        
        return $this->fcm->send($payload);
    }

    
     public function sendAttendance()
    {
        $payload = AttendancePayload::build('DEVICE_FCM_TOKEN', 'John Doe');
        return $this->fcm->send($payload);
    }

}
