<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Helper\Message;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;
use App\Services\FcmService;
use App\Notifications\Payloads\ExpensePayload;

class ExpenseController extends Controller
{
      protected $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }
    
    public function addExpense(Request $request){
        try {
            ApiLogService::info('Expense request received', $request->all()); 
            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|integer|exists:tbl_users,id',
                'expense_date'=> 'required|date',
                'expense_amount' => 'required|numeric|min:0',
                'remark' => 'required|string',
                'expense_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8048'
            ]);
            
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
            
            $userExist = User::findOrFail($request->staff_id);
            
             $imagePath = null;
            if ($request->hasFile('expense_image')) {
                $image = $request->file('expense_image');
                $imagePath = $image->store('uploads/expense_image', 'public'); 
            }

            $dataArray = array('expense_date'=>Carbon::parse($request->expense_date)->format('Y-m-d'),
            'expense_amount'=>$request->expense_amount,
            'remark'=>$request->remark,
            'staff_id'=>$userExist['id'],
            'expense_image' => $imagePath);
            $Expense =Expense::create($dataArray);
            
            if($Expense){
                
                 // ✅ Push notification to requesting user
                if ($userExist->device_id) {
                     $route = 'apply_reimbursements';
                    $payloadUser = ExpensePayload::build(
                        $userExist->device_id,
                        "Expense Request Submitted",
                        "Dear {$userExist->name} Your expense request on {$request->expense_date} for " . config('constants.INDIAN_RUPEE_SYMBOL') . "{$request->expense_amount} has been submitted.",
                        $route

                    );
                    $this->fcm->send($payloadUser);
                }

                // ✅ Push notification to Admin (user_id = 47)
                $admin = User::find(47);
                if ($admin && $admin->device_id) {
                    $route = 'expense_approval';
                    $payloadAdmin = ExpensePayload::build(
                        $admin->device_id,
                        "New Expense Request",
                        "{$userExist->name} applied for expense request on {$request->expense_date} for " . config('constants.INDIAN_RUPEE_SYMBOL') . "{$request->expense_amount}.",
                        $route
                    );
                    $this->fcm->send($payloadAdmin);
                }
                
                ApiLogService::success(Message::EXPENSE_SUCCESS, []);
                return ApiResponseService::success(Message::EXPENSE_SUCCESS, []); 
            }else{
                ApiLogService::success(Message::EXPENSE_UNSUCCESS, []);
                return ApiResponseService::success(Message::EXPENSE_UNSUCCESS, []);
            }
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    
       public function expenseList(Request $request){
        try {
            ApiLogService::info('Expense request received', $request->all()); 
            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|integer|exists:tbl_users,id',
            ]);
            $statusLabels = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];
            
        $expenseList = Expense::with('user')  
            ->where('staff_id', $request->staff_id)
            ->orderBy('id', 'desc')
            ->get();

       
        $expenseList = $expenseList->map(function($expense) use ($statusLabels) {
            return [
                'id' => $expense->id,
                'staff_id' => $expense->staff_id,
                'staff_name' => $expense->user->name ?? null, 
                'expense_date' => $expense->expense_date,
                'expense_amount' => $expense->expense_amount,
                'expense_image' =>  $expense->expense_image ? asset('storage/' . $expense->expense_image) : null,
                'status' => $expense->status, 
                'remark' => $expense->remark,
                'created_at' => $expense->created_at,
                'updated_at' => $expense->updated_at,
            ];
        });
            
           
            
            if(!empty($expenseList)){
                ApiLogService::success(Message::EXPENSE_SUCCESS_LIST, $expenseList);
                return ApiResponseService::success(Message::EXPENSE_SUCCESS_LIST,$expenseList);  
            }else{
                ApiLogService::success(Message::EXPENSE_UNSUCCESS_LIST, []);
                return ApiResponseService::success(Message::EXPENSE_UNSUCCESS_LIST, []);
            }
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
     public function allExpenseList(Request $request){
        try {
            ApiLogService::info('Expense request received', $request->all()); 
                
                $allExpenseList = Expense::with('user') 
    ->orderBy('id', 'desc')
    ->get()
    ->map(function ($item) {
        return [
            'id' => $item->id,
            'staff_id' => $item->staff_id,
            'staff_name' => $item->user->name ?? null, 
            'expense_date' => $item->expense_date,
            'expense_amount' => $item->expense_amount,
            'status' => $item->status,
            'remark' => $item->remark,
            'expense_image' => $item->expense_image ? asset('storage/' . $item->expense_image) : null,
        ];
    });

           
            if(!empty($allExpenseList)){
                ApiLogService::success(Message::EXPENSE_SUCCESS_LIST, $allExpenseList);
                return ApiResponseService::success(Message::EXPENSE_SUCCESS_LIST,$allExpenseList);  
            }else{
                ApiLogService::success(Message::EXPENSE_UNSUCCESS_LIST, []);
                return ApiResponseService::success(Message::EXPENSE_UNSUCCESS_LIST, []);
            }
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    public function expenseStatusUpdate(Request $request)
    {
        
        try 
        {
            ApiLogService::info('expense request received', $request->all());
             $validator = Validator::make($request->all(), [
                'staff_id' => 'required|integer|exists:tbl_users,id',
            ]);
            $expense = Expense::where('staff_id', $request->staff_id)->where('id',$request->id)->first();
            if ($expense) {
                $expense->status = $request->status;
                if($expense->save()){
                    
                    $userExist = User::find($expense->staff_id);
                     // ✅ Push notification to requesting user
                    if ($userExist->device_id && $request->status == 1 ) {
                         $route = 'apply_reimbursements';
                        $payloadUser = ExpensePayload::build(
                            $userExist->device_id,
                            "Expense Request Approved",
                            "Dear {$userExist->name} Your expense request is approved on {$expense->expense_date} for " . config('constants.INDIAN_RUPEE_SYMBOL') . "{$expense->expense_amount}.",
                            $route
    
                        );
                        $this->fcm->send($payloadUser);
                    }
                    
                    ApiLogService::success(Message::EXPENSE_STATUS_SUCCESS, []);
                    return ApiResponseService::success(Message::EXPENSE_STATUS_SUCCESS, []);  
                }else{
                    ApiLogService::success(Message::EXPENSE_STATUS_UNSUCCESS, []);
                    return ApiResponseService::success(Message::EXPENSE_STATUS_UNSUCCESS, []);
                }
            }
            else
            {
                ApiLogService::success(Message::EXPENSE_STATUS_UNSUCCESS, []);
                return ApiResponseService::success(Message::EXPENSE_STATUS_UNSUCCESS, []); 
            }
            
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    
}
