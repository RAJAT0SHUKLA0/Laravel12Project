<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ChequeInfo;
use Carbon\Carbon;
use App\Utils\Uploads;
use App\Services\ApiResponseService;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;
use App\Models\OrderAssign;
use App\Models\Seller;
use App\Models\BillSettlementDetail;

class BillController extends Controller
{
     const STATUS_CANCELLED = '2';
    public function addPayment(Request $request){
        if($request->type == 1  ){
            $validator = Validator::make($request->all(), [
                'amount'  => 'required',
                'type'    => 'required|integer',
                'seller_id' => 'required|integer|exists:tbl_sellers,id'
            ]);
        }
    
         if($request->type == 2 ){
             $validator = Validator::make($request->all(), [
                'amount'  => 'required',
                'date'    => 'required|date',
                'type'    => 'required|integer',
                'image'      => 'required|file|mimes:jpg,jpeg,png,webp|max:2048',
                'seller_id' => 'required|integer|exists:tbl_sellers,id'
            ]); 
         }
         
          if( $request->type == 3){
             $validator = Validator::make($request->all(), [
                'amount'  => 'required',
                'type'    => 'required|integer',
                'image'      => 'required|file|mimes:jpg,jpeg,png,webp|max:2048',
                'seller_id' => 'required|integer|exists:tbl_sellers,id'
            ]); 
         }
         

        if ($validator->fails()) {
            return ApiResponseService::validation("Validation failed", $validator->errors()->all());
        }
        
        $getPayment = new ChequeInfo();
        $getPayment->seller_id =$request->seller_id;
        $getPayment->type =$request->type;
        $getPayment->staff_id = auth()->id();
        $getPayment->date  =  now()->format('Y-m-d');
        $getPayment->amount  =  $request->amount;
        $getPayment->deduct_amount  =  $request->amount;
        $getPayment->cheque_clear_date  =  Carbon::parse($request->date)->format('Y-m-d');
        if ($request->hasFile('image')) {
            $path = Uploads::uploadImage($request->file('image'),'bill','bill');
            $getPayment->image = $path; 
        }

       $getPayment->save();
       return ApiResponseService::success("payment collect succcessfully", []);  
        
    }
    
    
    public function billSettlement(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'bill_id.*' => 'required|exists:tbl_transaction,id',
            'seller_id' => 'required|exists:tbl_sellers,id',
            'payment_mode' => 'required'
        ]);
        $status = $this->deductAmount($request->amount,$request->bill_id,$request->seller_id);
        
        return ApiResponseService::success("Bill settlement of ₹{$request->amount} completed successfully.");  
    }
    
    
    public function billList(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:tbl_sellers,id',
        ]);
        $seller = Seller::with(['transaction' => function ($query) {$query->where('status', '!=', 2)->orderByDesc('id');},'chequeInfos' => function ($query) {$query->where('status', 1)->latest()->limit(1);}])->find($request->seller_id);
        $billList = $seller->transaction;
        $latestCheque = $seller->chequeInfos->first();
        $deductAmount = 0;
        if ($latestCheque) {
            $totalTransactionAmount = $billList->sum('amount'); 
            $totaldeductAmount = $billList->sum('deduct_amount'); 
            if ($totalTransactionAmount == $totaldeductAmount) {
                   $billList[0]->deduct_amount = 0.00;

            }
        }
        $response = [
            "pendingAmt" => $deductAmount,
            "billList" => $billList
        ];
        return ApiResponseService::success("Bill list found", $response);
    }

    
    
    
    public function billListByRider(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');
        $riderId = auth()->id();
        $assignedOrderIds = $this->getAssignedOrderIds($riderId,$today);
        $existingBillIds = BillSettlementDetail::where('rider_id', $riderId)->pluck('bill_id');        
        $billList = Transaction::whereIn('order_id',$assignedOrderIds)->whereNotIn('id',$existingBillIds)->whereDate('date', $today)->where('status','!=',self::STATUS_CANCELLED)->orderBy('id','desc')->get();
        return ApiResponseService::success("Bill list found",$billList);  
    }
    
    public function billSettlementByRider(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'bill_id.*' => 'required|exists:tbl_transaction,id',
            'seller_id' => 'required|exists:tbl_sellers,id',
            'payment_mode' => 'required'
        ]);
        $billIdArray     = $request->bill_id;
        $billIdString    = implode(',', $billIdArray); 
        $riderId         = auth()->id();
        $sellerId        = $request->seller_id;
        $amount          = $request->amount;
        $paymentMode     = $request->payment_mode;
        $detailData = [
            'rider_id'      => $riderId,
            'seller_id'     => $sellerId,
            'amount'        => $amount,
            'payment_mode'  => $paymentMode,
            'bill_id'      => $billIdString,
        ];
        BillSettlementDetail::create($detailData);
        return ApiResponseService::success("Bill settlement Request Send Admin successfully.");  
    }
    
    private function deductAmount(float $amountToDeduct, array $bill_id,string $seller)
    {
        return DB::transaction(function () use ($amountToDeduct, $bill_id,$seller) {
            $selectedBills = Transaction::whereIn('id', $bill_id)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get();
            if ($selectedBills->isEmpty()) {
                return ;
            }
    
            $initialAmount = $amountToDeduct;
            $sellerId = $selectedBills->first()->seller_id;
            $firstSelectedCreatedAt = $selectedBills->first()->created_at;
            $bills = collect($selectedBills);
            $totalAvailable = $bills->sum('deduct_amount');
            if ($totalAvailable < $initialAmount) {
                $previousBills = Transaction::where('seller_id', $sellerId)
                    ->where('created_at', '<', $firstSelectedCreatedAt)
                    ->whereIn('status', [0, 1])
                    ->where('deduct_amount', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();
    
                $bills = $bills->merge($previousBills);
                $totalAvailable = $bills->sum('deduct_amount');
            }
            if ($totalAvailable < $initialAmount) {
                $remainingBills = Transaction::where('seller_id', $sellerId)
                    ->whereNotIn('id', $bills->pluck('id')) 
                    ->where('deduct_amount', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();
    
                $bills = $bills->merge($remainingBills);
                $totalAvailable = $bills->sum('deduct_amount');
            }
    
            if ($totalAvailable < $initialAmount) {
                return ;
            }
    
            foreach ($bills as $bill) {
                if ($amountToDeduct <= 0) break;
    
                $available = $bill->deduct_amount;
    
                if ($amountToDeduct >= $available) {
                    $amountToDeduct -= $available;
                    $bill->deduct_amount = 0;
                    $bill->status = 2;
                } else {
                    $bill->deduct_amount -= $amountToDeduct;
                    $amountToDeduct = 0;
                    $bill->status = 1;
                }
                $bill->save();
                $deductedAmount = $available - $bill->deduct_amount;
                $transactionHistory = new TransactionHistory();
                $transactionHistory->bill_id = $bill->id;
                $transactionHistory->date = now();
                $transactionHistory->seller_id = $bill->seller_id;
                $transactionHistory->staff_id = $bill->staff_id;
                $transactionHistory->deduct_amount = $deductedAmount;
                $transactionHistory->save();
                $chequAmtUpdate = ChequeInfo::where('status',1)->where('seller_id',$sellerId)->latest()->first();
                $chequAmtUpdate->deduct_amount  =  $deductedAmount;
                $chequAmtUpdate->save();
            }
    
            return true;
        });
    }
    
    
    private function getAssignedOrderIds($riderId, $date)
    {
        return OrderAssign::where('rider_id', $riderId)
            ->whereDate('assign_date', $date)
            ->get()
            ->flatMap(function ($assignment) {
                return array_map('trim', explode(',', $assignment->order_id));
            })
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();
    }
    
    
 public function riderPendingBills(Request $request)
{
    try {
        $billList = BillSettlementDetail::with([
            'seller:id,name',
            'rider:id,name'
        ])
        ->where('status', 0)
        ->get();
        $data = $billList->map(function ($bill) {
            return [
                'id'            => $bill->id,
                'order_id'       => $bill->bill_id,
                'amount'        => $bill->amount,
                'status'        => $bill->status,
                'date'    => $bill->created_at,
                'seller_name'   => $bill->seller->name ?? null,
                'rider_name'    => $bill->rider->name ?? null,
            ];
        });
        
        if($data->count() == 0)
        {
            return response()->json([
            'success' => true,
            'message' => 'No pending bills',
            'data'    => $data,
        ], 200);
        }
        else
        {
            return response()->json([
            'success' => true,
            'message' => 'Pending bills fetched successfully',
            'data'    => $data,
        ], 200);
        }

        

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

    
    
    public function riderBillSettlementApprove(Request $request)
{
     $validator = Validator::make($request->all(), [
                'bill_id'  => 'required|integer|exists:tbl_bill_settlement_details,id',
                'status'    => 'required|integer'
            ]);
        if ($validator->fails()) {
        return ApiResponseService::validation("Validation failed", $validator->errors()->all());
        }
        
    try {
       
        $id = $request->bill_id;
        $status = $request->status;
        return DB::transaction(function () use ($id, $status) {
            $RiderBillSettlement = BillSettlementDetail::find($id);

            if (!$RiderBillSettlement) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Record Not Found'
                ], 404);
            }
            
             if ($RiderBillSettlement->status == 1) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Bill Already Approved'
                ], 200);
            }

            $RiderBillSettlement->status = $status;
            $RiderBillSettlement->save();

            $DiductAmount = Transaction::where('order_id', $RiderBillSettlement->bill_id)->first();

            if (!$DiductAmount) {
                throw new \Exception("Transaction not found for this bill.");
            }

            if ($RiderBillSettlement->amount <= $DiductAmount->deduct_amount && $DiductAmount->status != 2) {
                $DiductAmount->payment_mode   = $RiderBillSettlement->payment_mode;
                $DiductAmount->deduct_amount -= $RiderBillSettlement->amount;
                $DiductAmount->status         = $DiductAmount->deduct_amount == 0 ? 2 : 1;
                $DiductAmount->save();

                TransactionHistory::create([
                    'bill_id'       => $RiderBillSettlement->bill_id,
                    'date'          => now(),
                    'seller_id'     => $RiderBillSettlement->seller_id,
                    'staff_id'      => $RiderBillSettlement->rider_id, 
                    'deduct_amount' => $RiderBillSettlement->amount,
                    'payment_mode'  => $RiderBillSettlement->payment_mode,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Bill Settled Successfully'
                ], 200);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pay amount is greater than remaining amount for this bill OR already completed'
                ], 400);
            }
        });
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => "Something went wrong: " . $e->getMessage()
        ], 500);
    }
}

    
}



