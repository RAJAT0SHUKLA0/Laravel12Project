<?php

namespace App\Http\Controllers\admin\seller;

use App\Http\Controllers\Controller;
use App\Repositories\Seller\SellerRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\seller\SellerRequest;
use App\Models\Area;
use App\Models\Order;
use App\Utils\Crypto;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\ChequeInfo;

class SellerController extends Controller
{
    
     private $SellerRepo;
    public function __construct(SellerRepositoryInterface $SellerRepositoryInterface)
    {
        $this->SellerRepo = $SellerRepositoryInterface;
    }
     public function index(Request $request)
    {
        
        $getStaffList= $this->SellerRepo->getAll();
        $getState  = $this->SellerRepo->getState();
        $getAllCity= $this->SellerRepo->getAllCity();


        
        if ($request->isMethod('post')) {
            $getAllCity= $this->SellerRepo->getCity($request->input('state_id'));
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                'name'         => $request->input('name'),
                'mobile'       => $request->input('mobile'),
                'status'       => $request->input('status'),
                'seller_id'     => $request->input('seller_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                

            ];
            if ($filterType === 'and') {
                if ($filters['name']) {
                    $getStaffList->where('name', 'like', '%' . $filters['name'] . '%');
                }
    
                if ($filters['mobile']) {
                    $getStaffList->where('mobile', 'like', '%' . $filters['mobile'] . '%');
                }
    
                if ($filters['status'] !== null) {
                    $getStaffList->where('status', $filters['status']);
                }
    
                if ($filters['seller_id']) {
                    $getStaffList->where('seller_id', $filters['seller_id']);
                }
    
               
                 if ($filters['state_id']) {
                    $getStaffList->where('state_id', $filters['state_id']);
                }
                 if ($filters['city_id']) {
                    $getStaffList->where('city_id', $filters['city_id']);
                }
                
            }
        }
        $getStaffList =$getStaffList->where('status','!=',3);
       $getStaffList1 = $getStaffList->paginate(10);
     return view('admin.seller.sellerList',compact('getStaffList1','getState','getAllCity'));
    }
    
    
    public function add()
    {
        $getState= $this->SellerRepo->getState();

        
        
        return view('admin.seller.sellerAdd',compact('getState'));
    }
    
    
    public function sellerSave(SellerRequest $SellerRequest){
        $validated = $SellerRequest->validated();
        $getSellerSaveStatus= $this->SellerRepo->create($validated);
        if($getSellerSaveStatus){
            return redirect()->back()->with("success","seller Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
    }
    
    public function getArea(Request $request){
      $getArea= Area::where('city_id',$request->city_id)->get();
      return response()->json(["data"=>$getArea]);
    }
    
    
     public function Edit($id)
    {
        
        
        $getStaffInfo = $this->SellerRepo->find($id);
        $getState= $this->SellerRepo->getState();
        $getAllCity= $this->SellerRepo->getAllCity();
        $getArea= $this->SellerRepo->getAllArea();
        
        
        return view('admin.seller.sellerAdd',compact('getState','getArea','getAllCity','getStaffInfo'));
    }
    
      public function sellerupdate(SellerRequest $SellerRequest,$id){
        $validated = $SellerRequest->validated();
        $getSellerSaveStatus= $this->SellerRepo->update($id,$validated);
        if($getSellerSaveStatus){
            return redirect()->route('sellerlist')->with("success","seller update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
      public function statusupdate($id,$status){
       $getSellerSaveStatus= $this->SellerRepo->statusupdate($id,$status);
        if($getSellerSaveStatus){
            return redirect()->route('sellerlist')->with("success","seller status Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
    
     public function delete($id){
        $getSellerSaveStatus= $this->SellerRepo->delete($id);
        if($getSellerSaveStatus){
            return redirect()->route('sellerlist')->with("success","seller delete Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
    



    public function sellerprofile($id)
    {
        $sellerId = Crypto::decryptId($id);
        $profileData = $this->SellerRepo->getAllSellerData($id);
        $currentYear = date('Y');
        $monthLabels = collect(range(1, 12))->map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)))->toArray();
        $orderDetails = OrderDetail::with(['order:id,seller_id,created_at', 'product:id,name'])->whereHas('order', function ($q) use ($sellerId, $currentYear) {$q->where('seller_id', $sellerId)->whereYear('created_at', $currentYear);})->get();
        $ordersByMonth = array_fill(1, 12, []);
        $productMonthlyData = [];
        $orderDetails->each(function ($detail) use (&$ordersByMonth, &$productMonthlyData) {
            $order = $detail->order;
            $product = $detail->product;
    
            if (!$order || !$product) return;
    
            $month = (int) $order->created_at->format('n');
            $ordersByMonth[$month][$order->id] = true;
    
            $productName = $product->name;
            $productMonthlyData[$productName][$month] = ($productMonthlyData[$productName][$month] ?? 0) + (int) $detail->qty;
        });
        $ordersData = collect(range(1, 12))->map(fn($m) => count($ordersByMonth[$m] ?? []))->toArray();
        $bardata = collect($productMonthlyData)->map(function ($monthlyQuantities, $product) {
            $data = collect(range(1, 12))->map(fn($m) => $monthlyQuantities[$m] ?? 0)->toArray();
            return [
                'name'  => $product,
                'data'  => $data,
                'color' => $this->generateRandomColor()
            ];
        })->values()->toArray();
       $transactions = Transaction::with('histories')
    ->where('seller_id', $sellerId)
    ->get();

    $txnCounts = $transactions->groupBy('status')->map->count()->toArray();
    
    $pendingAmount = $transactions
        ->where('seller_id', $sellerId)
        ->where('status', '!=','2')
        ->sum('amount');
    
    $completedAmount = $transactions
        ->where('seller_id', $sellerId)
        ->flatMap->histories
        ->sum('deduct_amount');
    
    $txnLabels    = ['pending', 'remaining', 'completed'];
    $txnLabelKeys = ['0', '1', '2'];
    $txnSeries = collect($txnLabelKeys)->map(fn($status) => (int) ($txnCounts[$status] ?? 0))->toArray();

        $chartData = [
            'Order' => 
            [
                'type'   => 'line',
                'labels' => $monthLabels,
                'series' => [['name' => 'Total Orders', 'data' => $ordersData]],
            ],
            'Product' => 
            [
                'type'   => 'bar',
                'labels' => $monthLabels,
                'series' => $bardata,
            ],
            'TxnStatus' => 
            [
                'type'   => 'pie',
                'labels' => $txnLabels,
                'series' => $txnSeries,
            ],
            'TransactionAmount'=>
            [
                'type'   => 'pie',
                'labels' => ['Pending Amount', 'Completed Amount'],
                'series' => [$pendingAmount, $completedAmount],
            ]
        ];
        
        $ChequeInfoData = ChequeInfo::where('seller_id',$sellerId)->where('type',2)->where('status',1)->where('is_already_submitted',0)->first();

        return view('admin.seller.sellerProfile', compact('profileData', 'chartData','ChequeInfoData'));
    }




    public function transactionReportSellerWise($sellerId){
        $TransactionReport = $this->SellerRepo->getTransactionReport($sellerId);
        return view('admin.seller.transactionReportSellerWise', compact('TransactionReport'));
    }


    private function generateRandomColor()
    {
        return 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',0.5)';
    }




    
    
    
    
}
