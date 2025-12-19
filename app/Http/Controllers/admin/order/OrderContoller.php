<?php

namespace App\Http\Controllers\admin\order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Seller;
use App\Models\User;
use App\Models\Area;
use App\Models\OrderAssign;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

use Mpdf\Mpdf;

use App\Utils\Crypto;
class OrderContoller extends Controller
{
    public function index(Request $request){
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
      $orderList=$query->orderby('id','desc')->paginate(10);
      $Seller = Seller::where('status',1)->get();
      $Staff = User::where('status',1)->get();
      return view('admin.order.order',compact('orderList','Seller','Staff'));
    }
    
    public function orderDetails($id){
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
        return view('admin.order.orderDetails',compact('orderList'));
    }
    
    
     public function orderAssign(Request $request){
        $sellerIdsWithOrders = Order::where('status','!=',6)->select('seller_id')->distinct()->pluck('seller_id')->toArray();
        $beatIds = Seller::whereIn('id', $sellerIdsWithOrders)->whereNotNull('beat_id')->distinct()->pluck('beat_id')->toArray();
        $area = Area::whereIn('id', $beatIds)->orderBy('name', 'asc')->select('id', 'name')->get();
        $rider = User::where('status',1)->where('role_id',4)->get();
        if ($request->ajax()) {
            if(isset($request->beat_id) && count($request->beat_id)>0){
                $sellerId= Seller::whereIn('beat_id',$request->beat_id)->pluck('id')->toArray();
                $assignedOrderIds = OrderAssign::whereIn('beat_id', $request->beat_id)->pluck('order_id')->flatMap(fn($orderId) => array_map('trim', explode(',', $orderId)))->filter()->map(fn($id) => (int) $id)->unique()->values();
                $query = Order::with(['orderDetails','seller:id,name,shop_name,latitude,longitude,profile_pic','staff:id,name','orderDetails.product:id,name,image','orderDetails.variant:id,name','orderDetails.productDetail:product_id,varient_id,retailer_price']);
                $orderList=$query->whereIn('seller_id',$sellerId)->whereNotIn('id',$assignedOrderIds)->orderby('id','desc')->paginate(20);
                return view('admin.load.orderAssignList', compact('orderList'))->render();
            }
            if(!isset($request->beat_id)){
              return [];  
            }
        }
        return view('admin.order.orderAssign',compact('area','rider'));
    }
    
                public function orderAssignSave(Request $request)
                {
                    $validator = Validator::make($request->all(), [
                        'rider_id' => 'required|exists:tbl_users,id',
                        'beat_id' => 'required|array',
                        'order_id' => 'required|array',
                    ]);
                
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
                    $undeliveredOrders = OrderAssign::where('rider_id', $request->rider_id)
                        ->get()
                        ->flatMap(function ($assign) {
                            return explode(',', $assign->order_id);
                        })
                        ->unique()
                        ->filter(function ($orderId) {
                            $status = Order::where('id', $orderId)->value('status');
                            return $status != 3; 
                        });
                
                    if ($undeliveredOrders->isNotEmpty()) {
                        return redirect()->back()->withErrors(['error' => 'Rider has undelivered orders. Cannot assign new ones.']);
                    }
                    $requestedOrderIds = $request->order_id;
                    $dataArray =array();
                    foreach ($request->beat_id as $beatId) {
                        $sellerIds = Seller::where('beat_id', $beatId)->pluck('id');
                
                        $orders = Order::whereIn('seller_id', $sellerIds)
                            ->whereIn('id', $requestedOrderIds)
                            ->where('status', '<>', 6)
                            ->pluck('id')
                            ->toArray();

                        if (count($orders)) {
                            $orderAssign = new OrderAssign();
                            $orderAssign->rider_id = $request->rider_id;
                            $orderAssign->beat_id = $beatId;
                            $orderAssign->order_id = implode(',', $orders);
                            $orderAssign->assign_date = now()->format('Y-m-d');
                            $orderAssign->save();
                            
                            Order::whereIn('id', $orders)->update(['status' => 6, 'order_assign_date' => now() , 'order_rider_id'=>$request->rider_id]);
                        }
                    }
                    return redirect()->back()->with('success', 'Orders assigned successfully.');
                }






     public function Status($id,$status){
         
         $order=Order::where('id',Crypto::decryptId($id))->first();
         if(!empty($order)){
             $pickup_date ='';
             $cancel_date ='';
            switch ($status) {
                case '0':
                    $statusDate = null;
                    break;
                case '2':
                    $pickup_date = now();
                    break;
                case '4':
                    $cancel_date = now();
                    break;
                default:
                    $statusDate = null;
            }
            $order ->status=  $status;
            $order ->pickup_date =$pickup_date??null;
            $order ->cancel_date =$cancel_date??null;
            $order ->save();
          return redirect()->back()->with('success','Order Cancelled Successfully.');
         }
    }
    
  
    
  

        public function generate_order_detail_invoice($id = '')
        {
            try {
                $decryptedId = Crypto::decryptId($id);
            } catch (\Exception $e) {
                abort(400, 'Invalid ID');
            }
        
            $hashName = sha1($decryptedId);
            $folderPath = public_path('storage/invoice');
            $fileName = $hashName . '.pdf';
            $filePath = "{$folderPath}/{$fileName}";
        
            if (File::exists($filePath)) {
                return response()->download($filePath);
            }
        
            $orderList = Order::with([
                'orderDetails',
                'seller:id,name,shop_name,latitude,longitude,profile_pic,beat_id,address,mobile',
                'staff:id,name',
                'orderDetails.product:id,name,image',
                'orderDetails.variant:id,name',
                'orderDetails.productDetail:product_id,varient_id,retailer_price,gst',
                'seller.area'
            ])->findOrFail($decryptedId);
             
        
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0775, true);
            }
            $mpdf = new \Mpdf\Mpdf([
                'format' => [420, 297], 
                'orientation' => 'P' ,
                'margin_left' => 5,
                'margin_right'=> 5,
                'margin_top'  => 10,
                'margin_bottom'=>10,
            ]);
             
            $html = view('pdf.invoice', compact('orderList'))->render();
            $mpdf->SetColumns(2, 'J');
            $mpdf->WriteHTML($html);
            $mpdf->WriteHTML($html);
            $mpdf->SetAutoPageBreak(true, 10);
            $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE); 
        
            return response()->download($filePath);
        }


        public function cancelOrder($orderid)
        {
             $id = Crypto::decryptId($orderid);
            $order = Order::find($id);
        
          $errorMessages = [
                2 => 'Cannot cancel, order already picked up.',
                3 => 'Cannot cancel, order already delivered.',
                4 => 'Order already cancelled.',
                6 => 'Cannot cancel, order already assigned.'
            ];

            if (array_key_exists($order->status, $errorMessages)) {
                return redirect()->back()->with('error', $errorMessages[$order->status]);
            }
        
            $order->update([
                'status' => 4,
                'cancel_date' => now()
            ]);
        
            return redirect()->back()->with('success', 'Order Cancelled Successfully.');
       }

}
