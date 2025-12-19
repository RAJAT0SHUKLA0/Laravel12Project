<?php
namespace App\Http\Controllers\admin\report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Models\Seller;
use App\Models\Order;
use App\Models\User;
use App\Utils\Crypto;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function billTransactionReport(Request $request)
    {
       
        $query = Transaction::with(['seller', 'staff','order']);

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
        }elseif ($request->filled('payment_status')) {
            $query->where('status', $request->payment_status);
        }elseif ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }
    
        $transactions = $query->orderBy('id','desc')->paginate(10);
    
        $seller = Seller::where('status',1)->get();
        $user = User::where('status',1)->get();
    
        return view('admin.report.BillTransactionReport', compact('transactions', 'seller', 'user'));
    }

 public function billTransactionHistory($id)
{
    $id = Crypto::decryptId($id);
    $transaction_history = TransactionHistory::where('bill_id', $id)
        ->with(['seller','staff']) 
        ->orderBy('id','desc')
        ->paginate(10); 

    return view('admin.report.BillTransactionHistory', compact('transaction_history'));
}

   


    public function orderReport(Request $request){
      $statusLabels = ['All Orders'=>'', 'Pending'=>'0', 'To Delivered'=>'1', 'Pickup'=>'2', 'Delivered'=>'3', 'Cancel'=>'4','Return'=>'5','Assign'=>'6'];
       $query = Order::with(['orderDetails','seller:id,name,shop_name,latitude,longitude,profile_pic','staff:id,name','orderDetails.product:id,name,image','orderDetails.variant:id,name','orderDetails.productDetail:product_id,varient_id,retailer_price']);
       if ($request->isMethod('post')) {
     
                if ($request->filled('order_status') && isset($statusLabels[$request->order_status])) {
                    $query->where('status', $statusLabels[$request->order_status]);
                }
        
                       $filters = [
                    'order_id'      => $request->input('order_id'),
                    'order_date'    => $request->input('order_date'),
                    'delivery_date' => $request->input('delivery_date'),
                    'staff_id'      => $request->input('staff_id'),
                    'seller_id'     => $request->input('seller_id'),
                    'order_status'  => $request->input('order_status'),
                    ];
                    
                    if ($filters['order_id']) {
                        $query->where('order_id', 'like', '%' . $filters['order_id'] . '%');
                    }
                    
                    if ($filters['order_date']) {
                        $query->whereDate('date', $filters['order_date']);
                    
                    }
                    
                    if ($filters['delivery_date']) {
                        $query->whereDate('delivery_date', $filters['delivery_date']);
                       
                    }
                    
                    if ($filters['staff_id']) {
                        $query->where('staff_id', $filters['staff_id']);
                    }
                    
                    if ($filters['seller_id']) {
                        $query->where('seller_id', $filters['seller_id']);
                    }
                    
                    if ($filters['order_status'] !== null && $filters['order_status'] !== '') {
                        $query->where('status', $filters['order_status']);
                    }

       }
       else {
        $query->whereDate('date',Carbon::today());
    }
       
      $orderList=$query->orderby('id','desc')->paginate(10);
      $Seller = Seller::where('status',1)->get();
      $Staff = User::where('status',1)->get();
        return view('admin.report.OrderReportList',compact('orderList','Seller','Staff'));
    }
    
    
     public function orderDetailReport($id){
         $orderId = Crypto::decryptId($id);

    $orderList = Order::with([
            'orderDetails',
            'seller:id,name,shop_name,latitude,longitude,profile_pic,mobile',
            'staff:id,name',
            'orderDetails.product:id,name,image,description',
            'orderDetails.variant:id,name',
            'orderDetails.productDetail:product_id,varient_id,retailer_price,gst'
        ])
        ->leftJoin('tbl_order_assign as oa', function($join) use ($orderId) {
            $join->whereRaw("FIND_IN_SET(?, oa.order_id)", [$orderId]);
        })
        ->where('tbl_order.id', $orderId)
        ->select('tbl_order.*', 'oa.rider_id', 'oa.assign_date')
        ->first();
        return view('admin.report.orderDetailReport',compact('orderList'));
    }
   
    
}
