<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Regularize;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Helper\Message;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\OrderAssign;
use App\Models\Order;
use App\Models\Expense;
use App\Models\ChequeInfo;
use App\Models\SubMenu;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionHistory;

class HistoryController extends Controller
{

    public function orderHistory(Request $request)
    {
        try {
            $userId = auth()->id();
            $user = User::with('role.permissions')->find($userId);

            if (!$user || !$user->role) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $today   = Carbon::today();
            $isAdmin = in_array($user->role_id, [1, 2]);

            $statusLabels = [
                0 => 'Pending',
                1 => 'To Deliver',
                2 => 'Pickup',
                3 => 'Delivered',
                4 => 'Cancel',
                5 => 'Return',
                6 => 'Assign'
            ];

            $query = Order::whereDate('date', $today);

            if (!$isAdmin) {
                $query->where('staff_id', $userId);
            }

            $countQuery = clone $query;

            $todaysOrders = $countQuery->count();

            $todaysOrdersByStatus = $countQuery->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $statusData = [];
            foreach ($statusLabels as $id => $label) {
                $statusData[$label] = $todaysOrdersByStatus[$id] ?? 0;
            }

            $orders = $query->with([
                'staff:id,name',
                'rider:id,name'
            ])->get()
                ->map(function ($order) use ($statusLabels) {
                    return [
                        'id'         => $order->id,
                        'order_id'         => $order->order_id,
                        'order_date'          => $order->date ? $order->date : 'N/A',
                        'order_assign_date'   => $order->order_assign_date ? $order->order_assign_date : 'N/A',
                        'order_pickup_date'   => $order->pickup_date ? $order->pickup_date : 'N/A',
                        'order_delivery_date' => $order->delivery_date ? $order->delivery_date : 'N/A',
                        'status'     => $statusLabels[$order->status] ?? $order->status,
                        'seller_name' => $order->seller->name ?? null,
                        'staff_name' => $order->staff->name ?? null,
                        'rider_name' => $order->rider->name ?? null,
                    ];
                });


            return response()->json([
                'status'  => true,
                'message' => "Today's order history fetched successfully",
                'data'    => [
                    'todays_total_orders'     => $todaysOrders,
                    'todays_orders_by_status' => $statusData,
                    'records'                 => $orders,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }




public function transactionHistory(Request $request)
{
    try {
        $userId = auth()->id();
        $user   = User::with('role.permissions')->find($userId);
        if (!$user || !$user->role) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $isAdmin = in_array($user->role_id, [1, 2]);

        $query = Transaction::with(['seller:id,name', 'staff:id,name', 'order:id,order_id']);
        
           if (!$isAdmin) {
            $query->where('staff_id', $userId);
        }

        // Filters
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('payment_status')) {
            $query->where('status', $request->payment_status);
        }
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(10);

        $formattedData = $transactions->through(function ($transaction) {
            $history = TransactionHistory::where('bill_id', $transaction->id)
                ->with(['seller:id,name', 'staff:id,name'])
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($h, $index) use($transaction) {
                      $paymentModes = [
                                1 => 'Cash',
                                2 => 'Cheque',
                                3 => 'UPI',
                            ];
                    return [
                        "S_No"           => $index + 1,
                        "Order_No"       => optional($transaction->order)->order_id ?? 'N/A',
                        "Transaction_No" => $transaction->transaction_no ?? 'N/A',
                        "Seller"         => optional($h->seller)->name ?? 'N/A',
                        "Staff"          => optional($h->staff)->name ?? 'N/A',
                        "Date"           => $h->date ?? 'N/A',
                        "Payment_Mode"   => $paymentModes[$h->payment_mode] ?? 'N/A',
                        "Deduct_Amount"  => $h->deduct_amount ?? '0.00',
                    ];
                });

            return [
                'id'             => $transaction->id,
                'transaction_no' => $transaction->transaction_no,
                'date'           => $transaction->date,
                'payment_mode'   => $transaction->payment_mode,
                'amount'         => $transaction->amount,
                'deduct_amount'  => $transaction->deduct_amount,
                'status'         => $transaction->status,
                'seller_name'    => optional($transaction->seller)->name ?? 'N/A',
                'staff_name'     => optional($transaction->staff)->name ?? 'N/A',
                'order_no'       => optional($transaction->order)->order_id ?? 'N/A',
                'history'        => $history, 
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Bill transaction report fetched successfully',
            'data'    => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
                'data'         => $formattedData,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => $e->getMessage()
        ], 500);
    }
}



}
