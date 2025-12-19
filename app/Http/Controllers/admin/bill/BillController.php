<?php

namespace App\Http\Controllers\admin\bill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Models\TransactionByCheque;
use Illuminate\Support\Facades\DB;
use App\Repositories\Bill\BillRepositoryInterface;
use App\Http\Requests\bill\BillRequest;
use App\Models\ChequeInfo;
use App\Models\Order;
use App\Models\BillSettlementDetail;
use App\Utils\Crypto;
use Carbon\Carbon;

class BillController extends Controller
{
    private $billRepo;
    public function __construct(BillRepositoryInterface $BillRepositoryInterface)
    {
        $this->billRepo = $BillRepositoryInterface;
    }


    public function index()
    {
        $Seller =  $this->billRepo->getSeller();
        $orderList = $this->billRepo->getAll();
        return view('admin.bill.billList', compact('Seller', 'orderList'));
    }



    public function saveCheque(BillRequest $BillRequest)
    {
        $validated = $BillRequest->validated();
        $getchequeRequest = $this->billRepo->create($validated);
        if ($getchequeRequest) {
            return redirect()->back()->with("success", "cheque Save Successfully");
        } else {
            return redirect()->back()->with("errror", "something went wrong");
        }
    }

    public function approveCheque($id, $status)
    {
        $getstatuscheque =  $this->billRepo->status($id, $status);
        return redirect()->back()->with("success", "cheque status change Successfully");
    }

    public function billSettlement(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'bill_id.*' => 'required|exists:tbl_transaction,id',
            'seller_id' => 'required',
            'cheque_id' => 'nullable',
            'payment_mode' => 'required'
        ]);


        $status = $this->deductAmount($request->amount, $request->bill_id, $request->seller_id, $request->payment_mode, $request->cheque_id);
        return redirect($request->seller_id)->with('success', "Bill settlement of ₹{$request->amount} completed successfully.");
    }

    private function deductAmount(float $amountToDeduct, array $bill_id, string $seller, int $payment_mode, int $cheque_id)
    {
        return DB::transaction(function () use ($amountToDeduct, $bill_id, $seller, $payment_mode, $cheque_id) {

            $cheque = ChequeInfo::find($cheque_id);
            if ($cheque) {
                $cheque->is_already_submitted = 1;
                $cheque->save();
            }


            $selectedBills = Transaction::whereIn('id', $bill_id)
                ->where('deduct_amount', '>', 0)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get();

            if ($selectedBills->isEmpty()) {
                return;
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
                return;
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
                $bill->payment_mode = $payment_mode;
                $bill->save();

                $deductedAmount = $available - $bill->deduct_amount;

                $transactionHistory = new TransactionHistory();
                $transactionHistory->bill_id = $bill->id;
                $transactionHistory->date = now();
                $transactionHistory->seller_id = $bill->seller_id;
                $transactionHistory->staff_id = $bill->staff_id;
                $transactionHistory->deduct_amount = $deductedAmount;
                $transactionHistory->payment_mode = $payment_mode;
                $transactionHistory->save();
            }

            return true;
        });
    }





    public function riderBillList(Request $request)
    {
        $Seller = $this->billRepo->getSeller();
        $query = BillSettlementDetail::with(['seller', 'rider']);
        if ($request->isMethod('post')) {
            if (!empty($request->seller_id)) {
                $query->where('seller_id', $request->seller_id);
            }
            if (!empty($request->date)) {
                $query->whereDate('created_at', $request->date);
            }
        }
        $orderList = $query->paginate(10);

        return view('admin.bill.riderBillList', compact('Seller', 'orderList'));
    }


    public function approveRiderBill($id, $status)
    {
        try {
            return DB::transaction(function () use ($id, $status) {
                $RiderBillSettlement = BillSettlementDetail::find(Crypto::decryptId($id));

                if (!$RiderBillSettlement) {
                    return redirect()->back()->with("error", "Record Not Found");
                }
                 if ($RiderBillSettlement->status ==1) {
                    return redirect()->back()->with("error", "Bill Aready Approved");
                }

                $RiderBillSettlement->status = $status;
                $RiderBillSettlement->save();

                $DiductAmount = Transaction::where('order_id', $RiderBillSettlement->bill_id)->first();

                if (!$DiductAmount) {
                    throw new \Exception("Transaction not found for this bill.");
                }

                if ($RiderBillSettlement->amount <= $DiductAmount->deduct_amount && $DiductAmount->status != 2) {
                    $DiductAmount->payment_mode = $RiderBillSettlement->payment_mode;
                    $DiductAmount->deduct_amount = $DiductAmount->deduct_amount - $RiderBillSettlement->amount;
                    $DiductAmount->status = $DiductAmount->deduct_amount == 0 ? 2 : 1;
                    $DiductAmount->save();
                    TransactionHistory::create([
                        'bill_id'       => $RiderBillSettlement->bill_id,
                        'date'          => now(),
                        'seller_id'     => $RiderBillSettlement->seller_id,
                        'staff_id'      => $RiderBillSettlement->rider_id,
                        'deduct_amount' => $RiderBillSettlement->amount,
                        'payment_mode'  => $RiderBillSettlement->payment_mode,
                    ]);

                    return redirect()->back()->with("success", "Bill Settled Successfully");
                } else {
                    return redirect()->back()->with("error", "Pay amount is greater than remaining amount for this bill OR already completed");
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with("error", "Something went wrong: " . $e->getMessage());
        }
    }
}
