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

class HomeController extends Controller
{
      public function getHomeScreenData(Request $request)
    {
        try{
            ApiLogService::info('Home request received', $request->all());
             $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:tbl_users,id'
            ]);
             $user = User::with('role.permissions')->find($request->user_id);
                if (!$user || !$user->role) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                $menuGrouped = $user->role->permissions->groupBy(fn($p) => $p->menu_id)->filter();
                $components = [];
                $menuSummaries = [];
                $processedMenus = [];
                foreach ($menuGrouped as $menuId => $permsInMenu) {
                    $allMenus = $permsInMenu->flatMap(fn($perm) => $perm->menus)->unique('id')->sortBy('orderby')->values();
                    foreach ($allMenus as $menu) {
                        if (!$menu || in_array($menu->id, $processedMenus)) continue;
                        $processedMenus[] = $menu->id;
                        $allSubs = $permsInMenu->map(fn($p) => $p->submenus)->flatten(1)->filter()->unique('id');
                        $submenusForMenu = $allSubs->filter(fn($s) => $s->menu_id == $menu->id && $s->parent_id == 0)->sortBy('order')->values();
                        if ($submenusForMenu->isEmpty()) continue;
                        $submenuCount = $submenusForMenu->count();
                        $menuSummary = ['title' => $menu->name,'order'=> $menu->orderby,'count' => $submenuCount,'sub_menu' => []];
                        $grouped = $submenusForMenu->groupBy(fn($s) => optional($s->submenutype)->id);
                        foreach ($grouped as $typeId => $grp) {
                            $typeName = optional($grp->first()->submenutype)->name ?: 'Other';
                            $items = $grp->map(function ($s) {
                                return [
                                    'parent_id' => $s->id,
                                    'item_id' => "submenu_{$s->id}",
                                    'title' => $s->name,
                                    'order'=> $s->order,
                                    'image_url' => asset("storage/uploads/sub-menu/{$s->image}"),
                                    'background_color_hex' => $s->color_code,
                                    'action' => [
                                        'type' => 'NAVIGATE',
                                        'payload' => [
                                            'route' =>  $s->action ?? ''
                                        ]
                                    ]
                                ];
                            });

                            $component = [
                                'component_id' => 'grid_' . strtolower(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_type' => strtoupper(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_data' => [
                                    'items' => $items->values()
                                ]
                            ];
                            $menuSummary['sub_menu'][] = $component;
                        }
                        $menuSummaries[] = $menuSummary;
                    }
                }
                if (!empty($menuSummaries)) {
                    array_unshift($components, [
                        'component_id' => 'menu_summary_list',
                        'component_type' => 'MENU_SUMMARY_LIST',
                        'component_data' => $menuSummaries
                    ]);
                }
                return response()->json(['status'   => true,'role'=>$user->role->name,
                        'message' => "Home UI for {$user->name}",
                    'data' => [
                        
                        'screen_title' => "Hey, {$user->name}!",
                        'columns' => config('constants.SUB_MENU_COLUMNS'),
                        'components' => $menuSummaries
                    ],
                    'meta' => array_merge([
                        'version' => '1.0.1',
                        'timestamp' => now()->toIso8601String()
                    ], $this->getLastUpdatedTimestamps())
                ]);

        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
       
    }
    
    
    public function getSubMenuScreenData(Request $request)
    {
        try{
            ApiLogService::info('Home request received', $request->all());
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:tbl_users,id',
                'parent_id' => 'required|integer'

            ]);
             $user = User::with('role.permissions')->find($request->user_id);
                if (!$user || !$user->role) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                $menuGrouped = $user->role->permissions->groupBy(fn($p) => $p->menu_id)->filter();
                $components = [];
                $menuSummaries = [];
                $processedMenus = [];
                foreach ($menuGrouped as $menuId => $permsInMenu) {
                        $allSubs = $permsInMenu->map(fn($p) => $p->submenus)->flatten(1)->filter()->unique('id');
                        $parentSubmenu = $allSubs->firstWhere('id', $request->parent_id);
                        $submenusForMenu = $allSubs->filter(fn($s) =>$s->parent_id == $request->parent_id)->sortBy('order')->values();
                        if ($submenusForMenu->isEmpty()) continue;
                        $submenuCount = $submenusForMenu->count();
                        $menuSummary = ['title' => $parentSubmenu->name,'order'=> $parentSubmenu->order,'count' => $submenuCount,'sub_menu' => []];
                        $grouped = $submenusForMenu->groupBy(fn($s) => optional($s->submenutype)->id);
                        foreach ($grouped as $typeId => $grp) {
                            $typeName = optional($grp->first()->submenutype)->name ?: 'Other';
                            $items = $grp->map(function ($s) {
                                return [
                                    'item_id' => "submenu_{$s->id}",
                                    'title' => $s->name,
                                    'order'=> $s->order,
                                    'image_url' => asset("storage/uploads/sub-menu/{$s->image}"),
                                    'background_color_hex' => $s->color_code,
                                    'action' => [
                                        'type' => 'NAVIGATE',
                                        'payload' => [
                                            'route' =>  $s->action ?? ''
                                        ]
                                    ]
                                ];
                            });

                            $component = [
                                'component_id' => 'grid_' . strtolower(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_type' => strtoupper(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_data' => [
                                    'items' => $items->values()
                                ]
                            ];
                            $menuSummary['sub_menu'][] = $component;
                        }
                        $menuSummaries[] = $menuSummary;
                }
                
                if (!empty($menuSummaries)) {
                    array_unshift($components, [
                        'component_id' => 'menu_summary_list',
                        'component_type' => 'MENU_SUMMARY_LIST',
                        'component_data' => $menuSummaries
                    ]);
                }
                return response()->json(['status'   => true,'role'=>$user->role->name,
                        'message' => "child menu list Found",
                    'data' => [
                        
                        'screen_title' => "Hey, {$user->name}!",
                        'columns' => config('constants.SUB_MENU_COLUMNS'),
                        'components' => $menuSummaries
                    ],
                    
                ]);

        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
       
    }

    private function getLastUpdatedTimestamps(): array
    {
        return [
            'last_attendance_updated_at' => optional(Attendance::latest('updated_at')->first())->updated_at?->format('Y-m-d h:i:s A'),
            'last_leave_updated_at' => optional(Leave::latest('updated_at')->first())->updated_at?->format('Y-m-d h:i:s A'),
            'last_regularize_updated_at' => optional(Regularize::latest('updated_at')->first())->updated_at?->format('Y-m-d h:i:s A'),
            'location_track_time' => config('constants.LOCATION_TRACK_TIME')
        ];
    }
    
    
    
    
    public function initiateHome(Request $request)
    {
        $userId = auth()->id();
        $user = User::find($userId);
        $today = Carbon::today();
        $existingAttendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        $existingLeave = Leave::where('user_id', $userId)->whereDate('date', $today)->first();
        $existingLeaveCount = Leave::where('user_id', $userId)->count();
        $existingRegularize = Regularize::where('user_id', $userId)->whereDate('date', $today)->first();
        $intimeStatus = !empty($existingAttendance?->in_time);
        $outtimeStatus = !empty($existingAttendance?->out_time);
        $leaveStatusLabels = [0 => 'Pending', 1 => 'Approved', 2 => 'Rejected'];
        $orderStatusLabels = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Pickup',
            3 => 'Deliver',
            4 => 'Cancel',
            5 => 'Return',
            6 => 'Assign',
        ];
        $transactionStatusLabels = [0 => 'Pending', 1 => 'Completed', 2 => 'Failed'];
        $attendanceData = [
            'in_time'        => $existingAttendance->in_time ?? 'No data available',
            'out_time'       => $existingAttendance->out_time ?? 'No data available',
            'in_time_status' => $intimeStatus,
            'out_time_status'=> $outtimeStatus,
        ];
        $leaveData = [
            'status'     => $leaveStatusLabels[optional($existingLeave)->status] ?? '',
            'start_date' => $existingLeave->start_date ?? '',
            'end_date'   => $existingLeave->end_date ?? '',
            'count'      => $existingLeaveCount,
        ];
        $regularizeData = [
            'status' => $leaveStatusLabels[optional($existingRegularize)->status] ?? '',
            'date'   => $existingRegularize->date ?? '',
            'remark' => $existingRegularize->remark ?? '',
        ];
        $assignedOrderIds = OrderAssign::where('rider_id', $userId)->whereDate('created_at', $today)
            ->pluck('order_id')
            ->flatMap(fn($ids) => array_map('trim', explode(',', $ids)))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique();
        $orders = Order::with([
                'staff:id,name',
                'seller:id,name,shop_name,profile_pic,address,beat_id',
                'seller.area:id,name',
                'transaction:id,order_id,status',
                'orderDetails.product:id,name,image',
                'orderDetails.variant:id,name',
            ])
            ->whereIn('id', $assignedOrderIds)
            ->whereDate('created_at', $today)
            ->get();
        $orderList = $orders->map(function ($order) use ($orderStatusLabels, $transactionStatusLabels) {
            return [
                'order_id'           => $order->id,
                'order_no'           => $order->order_id ?? '',
                'staff_name'         => $order->staff->name ?? '',
                'seller_name'        => $order->seller->name ?? '',
                'beat_name'          => $order->seller->area->name ?? '',
                'seller_shop_name'   => $order->seller->shop_name ?? '',
                'seller_profile_pic' => $order->seller?->profile_pic
                    ? asset("storage/uploads/profile/{$order->seller->profile_pic}")
                    : '',
                'seller_address'     => $order->seller->address ?? '',
                'total_price'        => $order->total_price,
                'status'             => $orderStatusLabels[$order->status] ?? 'Unknown',
                'transaction_status' => optional($order->transaction->first())->status
                    ? ($transactionStatusLabels[$order->transaction->first()->status] ?? $transactionStatusLabels[0])
                    : $transactionStatusLabels[0],
                'order_date'         => Carbon::parse($order->date)->format('Y-m-d'),
                'order_details'      => $order->orderDetails->map(fn($detail) => [
                    'product_name'  => $detail->product->name ?? '',
                    'variant_name'  => $detail->variant->name ?? '',
                    'qty'           => $detail->qty,
                    'product_image' => optional($detail->product)->image
                        ? asset("storage/uploads/product/{$detail->product->image}")
                        : '',
                    'per_price'     => $detail->per_price,
                    'total_price'   => $detail->qty * $detail->per_price,
                ]),
            ];
        });
        $statusCounts = $orderList->pluck('status')->countBy()->all();
        $completeStatusCounts = collect($orderStatusLabels)
            ->mapWithKeys(fn($label) => [$label => $statusCounts[$label] ?? 0])
            ->all();
        $paymentHistory = ChequeInfo::where('staff_id', $userId)->pluck('type')->countBy()->all();
        $paymentHistoryCount = ChequeInfo::where('staff_id', $userId)->pluck('type')->count();

         $knownTypes = ['cash', 'Cheque', 'upi'];
        $completePaymentCounts = collect($knownTypes)
        ->mapWithKeys(fn($type) => [$type => $paymentHistoryCounts[$type] ?? 0])
        ->all();
        $modules = [
            'attendance'    => $attendanceData,
            'leave'         => $leaveData,
            'regularize'    => $regularizeData,
            'orders'        => $orderList->values(),
            'status_counts' => $completeStatusCounts,
            'payment_count' =>$paymentHistoryCount,
            'payment_history_count' =>$completePaymentCounts
        ];
        
        $response =  [
                        
                        'screen_title' => "Hey, {$user->name}!",
                        'components' => $modules
                    ];
    
        return ApiResponseService::success('Home Screen Data Found', $response);
    }
    
    
    // NEW HOME SCREEN CODE 
    public function initiateHomeV2(Request $request)
    {
       
            try{
            ApiLogService::info('Home request received', $request->all());
            
             $validator = Validator::make($request->all(), [
                // 'user_id' => 'required|integer|exists:tbl_users,id',
                'device_id' => 'required|string',
            ]);
           
            if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
                ], 422);
            }
         
            $userId = auth()->id();
            
              $user = User::with('role.permissions')->find($userId);
                if (!$user || !$user->role) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                
        
            if ($user->device_id !== $request->device_id) {
                $user->device_id = $request->device_id;
                $user->save();
                ApiLogService::info('Device ID updated', ['user_id' => $user->id, 'new_device_id' => $request->device_id]);
            }

                
            $today = Carbon::today();
            $existingAttendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
            
            $existingLeave = Leave::where('user_id', $userId)->whereDate('date', $today)->first();
            $existingLeaveCount = Leave::where('user_id', $userId)->count();
           
            $existingRegularize = Regularize::where('user_id', $userId)->whereDate('date', $today)->first();
            $existingRegularizeCount = Regularize::where('user_id', $userId)->count();
           
            $intimeStatus = !empty($existingAttendance?->in_time);
            $outtimeStatus = !empty($existingAttendance?->out_time);
           
            $existingExpense = Expense::where('staff_id', $userId)->whereDate('expense_date', $today)->first();
            $existingExpenseCount = Expense::where('staff_id', $userId)->count();
            
            $leaveStatusLabels = [0 => 'Pending', 1 => 'Approved', 2 => 'Rejected'];
            $expenseStatusLabels = [0 => 'Pending', 1 => 'Approved', 2 => 'Rejected'];
            
            $allStaff = User::where('status',1)->where('role_id','!=',1)->select('id','name','email','mobile')->get();
      
            $presentStaffIds = Attendance::whereDate('date', $today)->pluck('user_id')->toArray();
            $presentStaff = $allStaff->whereIn('id', $presentStaffIds)->count();
            $absentStaff  = $allStaff->whereNotIn('id', $presentStaffIds)->count();

            if(auth()->user()->role_id == 1)
            {
                $totalLeavesCount = Leave::all()->count();
                $todayLeavesCount = Leave::whereDate('date',$today)->count();
            }
            else
            {
                $totalLeavesCount = Leave::where('user_id',$userId)->get()->count();
                $todayLeavesCount = Leave::whereDate('date',$today)->where('user_id',$userId)->count(); 
            }
            
            
             if(auth()->user()->role_id == 1)
            {
                $totalRegulizeCount = Regularize::all()->count();
                $todayRegulizeCount = Regularize::whereDate('date',$today)->count();
            }
            else
            {
               $totalRegulizeCount = Regularize::where('user_id',$userId)->get()->count();
               $todayRegulizeCount = Regularize::whereDate('date',$today)->where('user_id',$userId)->get()->count();
            }
            
             
            if(auth()->user()->role_id == 1)
            {
                $totalExpenseCount = Expense::all()->count();
                $todayExpenseCount = Expense::whereDate('expense_date',$today)->count();
            }
            else
            {
                $totalExpenseCount = Expense::where('staff_id',$userId)->get()->count();
                $todayExpenseCount = Expense::whereDate('expense_date',$today)->where('staff_id',$userId)->get()->count();
            }
            
            $allSellerCount =  Seller::where('status','!=',3)->get()->count();
            $activeSellerCount =  Seller::where('status',1)->get()->count();
            $inactiveSellerCount =  Seller::where('status',0)->get()->count();
            
            $topSellingProducts = OrderDetail::with('product')
            ->select('product_id', DB::raw('COUNT(product_id) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->get()
            ->pluck('total_sold', 'product.name')   
            ->toArray();
            $allProducts = Product::where('status','!=',3)->where('is_delete',0)->get()->count();
             $activeProducts = Product::where('status',1)->where('is_delete',0)->get()->count();
             $inactiveProducts = Product::where('status',0)->where('is_delete',0)->get()->count();
             
             $sevenDaysAgo = Carbon::today()->subDays(7);
             $thirtyDaysAgo = Carbon::today()->subDays(30);
             
             $orderCounts = [
                'all'       => Order::count(),
                'today'     => Order::whereDate('date', $today)->count(),
                'last_7'    => Order::whereBetween('date', [$sevenDaysAgo, $today])->count(),
                'last_30'   => Order::whereBetween('date', [$thirtyDaysAgo, $today])->count(),
            ];
            $todayOrdersByStatus = [
                'pending'   => Order::whereDate('delivery_date', $today)->where('status', 0)->count(),
                'to_deliver'=> Order::whereDate('delivery_date', $today)->where('status', 1)->count(),
                'delivered' => Order::whereDate('delivery_date', $today)->where('status', 3)->count(),
                'cancelled' => Order::whereDate('delivery_date', $today)->where('status', 4)->count(),
                'returned'  => Order::whereDate('delivery_date', $today)->where('status', 5)->count(),
                'assigned'  => Order::whereDate('delivery_date', $today)->where('status', 6)->count(),
                'pickup'  => Order::whereDate('pickup_date', $today)->count(),
            ];
            $allOrdersByStatus = [
                'pending'   => Order::where('status', 0)->count(),
                'to_deliver'=> Order::where('status', 1)->count(),
                'delivered' => Order::where('status', 3)->count(),
                'cancelled' => Order::where('status', 4)->count(),
                'returned'  => Order::where('status', 5)->count(),
                'assigned'  => Order::where('status', 6)->count(),
            ];
             
            $isShow = false;
            if(!empty($existingAttendance)){
              $isShow = true;  
            }
             $attendanceData = [
            'in_time'        => $existingAttendance->in_time ?? 'No data available',
            'out_time'       => $existingAttendance->out_time ?? 'No data available',
            'in_time_status' => $intimeStatus,
            'out_time_status'=> $outtimeStatus,
                ];
                
            $leaveData = [
             'status'     => $leaveStatusLabels[optional($existingLeave)->status] ?? '',
            'start_date' => $existingLeave->start_date ?? '',
            'end_date'   => $existingLeave->end_date ?? '',
            'existingLeaveCount'      => $existingLeaveCount,
            ];
            
            $regularizeData = [
            'status' => $leaveStatusLabels[optional($existingRegularize)->status] ?? '',
            'date'   => $existingRegularize->date ?? '',
            'remark' => $existingRegularize->remark ?? '',
             'existingRegularizeCount'      => $existingRegularizeCount,
            ];
            
             $expenseData = [
             'status' => $expenseStatusLabels[optional($existingExpense)->status] ?? '',
            'date'   => $existingExpense->expense_date ?? '',
            'remark' => $existingExpense->remark ?? '',
             'existingExpenseCount'      => $existingExpenseCount,
            ];
            
             $staffData = [
            'totalStaff'        => $allStaff->count() ?? 'No data available',
            'presentStaff'       => $presentStaff ?? 'No data available',
            'absentStaff' =>        $absentStaff?? 'No data available'
                ];
                
             $manageEmployee = [
            'totalLeaveCount'        => $totalLeavesCount ?? 'No data available',
            'todayLeaveCount'       => $todayLeavesCount ?? 'No data available',
             'totalRegulizeCount'        => $totalRegulizeCount ?? 'No data available',
            'todayRegulizeCount'       => $todayRegulizeCount ?? 'No data available',
             'totalExpenseCount'        => $totalExpenseCount ?? 'No data available',
            'todayExpenseCount'       => $todayExpenseCount ?? 'No data available',
             ];
             
             $manageSeller = [
            'totalSellerCount'        => $allSellerCount ?? 'No data available',
            'activeSellerCount'       => $activeSellerCount ?? 'No data available',
             'inactiveSellerCount'        => $inactiveSellerCount ?? 'No data available'
             ];
             
            $manageProducts = [
            'allProducts'        => $allProducts ?? 'No data available',
            'activeProducts'       => $activeProducts ?? 'No data available',
            'inactiveProducts'        => $inactiveProducts ?? 'No data available',
            'topSellingProducts'        => $topSellingProducts ?? 'No data available'
            ];
            
             $manageOrders = [
            'orderCounts'        => $orderCounts ?? 'No data available',
             'allOrdersByStatus'        => $allOrdersByStatus ?? 'No data available',
            'todayOrdersByStatus'       => $todayOrdersByStatus ?? 'No data available'
            ];
          
          
                $menuGrouped = $user->role->permissions->groupBy(fn($p) => $p->menu_id)->filter();
              
                $components = [];
                $menuSummaries = [];
                $processedMenus = [];
                $submenuarraynames =SubMenu::where('status',1)->where('is_delete',0)->select('name')->get()->toArray();
                   
                foreach ($menuGrouped as $menuId => $permsInMenu) {
                    $allMenus = $permsInMenu->flatMap(fn($perm) => $perm->menus)->unique('id')->sortBy('orderby')->values();
                    
                    foreach ($allMenus as $menu) {
                        if (!$menu || in_array($menu->id, $processedMenus)) continue;
                        
                        $processedMenus[] = $menu->id;
                        $allSubs = $permsInMenu->map(fn($p) => $p->submenus)->flatten(1)->filter()->unique('id');
                        $submenusForMenu = $allSubs->filter(fn($s) => $s->menu_id == $menu->id && $s->parent_id == 0)->sortBy('order')->values();
                       
                        if ($submenusForMenu->isEmpty()) continue;
                        
                        $submenuCount = $submenusForMenu->count();
                        $menuSummary = ['title' => $menu->name,'order'=> $menu->orderby,'count' => $submenuCount,'sub_menu' => []];
                        $grouped = $submenusForMenu->groupBy(fn($s) => optional($s->submenutype)->id);
                       
                        foreach ($grouped as $typeId => $grp) {
                            $typeName = optional($grp->first()->submenutype)->name ?: 'Other';
                           
                            $items = $grp->map(function ($s) use ($attendanceData,$leaveData,$regularizeData,$expenseData,$staffData,$manageEmployee,$manageSeller,$manageProducts,$manageOrders,$isShow){
                               
                                $dataList = [];
                              
                                 $menuMapping = [
                                    'Mark Attendance'         => $attendanceData,
                                    'View Leaves'             => $leaveData,
                                    'Regularization Requests' => $regularizeData,
                                    'View Expenses'           => $expenseData,
                                    'Staff Report'            => $staffData,
                                    'Manage Employees'        => $manageEmployee,
                                    'Sellers'                 => $manageSeller,
                                    'Products'                => $manageProducts,
                                    'Manage Orders'           => $manageOrders,
                                ];
                               
                                $dataList = $menuMapping[$s->name] ?? [];
                                
                                // if($s->name == 'Mark Attendance'){ $dataList = $attendanceData; } 
                                // if($s->name == 'View Leaves' ){ $dataList = $leaveData; } 
                                // if($s->name == 'Regularization Requests'){ $dataList = $regularizeData; } 
                                // if($s->name == 'View Expenses'){ $dataList = $expenseData; }
                                // if($s->name == 'Staff Report'){ $dataList = $staffData; } 
                                // if($s->name == 'Manage Employees'){ $dataList = $manageEmployee; }
                                // if($s->name == 'Sellers'){ $dataList = $manageSeller; } 
                                // if($s->name == 'Products'){ $dataList = $manageProducts; }
                                // if($s->name == 'Manage Orders'){ $dataList = $manageOrders; }
                                
                              
                                    // if(count($dataList)==0 ){
                                    //   $isShow = false;  
                                    // }
                                    
                                $isShow = true; 

                                if (empty($dataList) || collect($dataList)->every(function ($v) {
                                    // treat these as "empty" values
                                    return $v === null || $v === '' || $v === 0 || $v === false || $v === 'No data available';
                                })) {
                                    $isShow = false;
                                }
                                
                                if($s->name == 'Mark Attendance')
                                {
                                        $isShow = true;
                                }

                            
                                    
                                return [
                                    'parent_id' => $s->id,
                                    'item_id' => "submenu_{$s->id}",
                                    'title' => $s->name,
                                    'order'=> $s->order,
                                    'image_url' => asset("storage/uploads/sub-menu/{$s->image}"),
                                    'background_color_hex' => $s->color_code,
                                    'action' => [
                                        'type' => 'NAVIGATE',
                                        'payload' => [
                                            'route' =>  $s->action ?? ''
                                        ]
                                    ],
                                     'isshow' =>$isShow,
                                    'data' =>$dataList
                                ];
                            });

                            $component = [
                                'component_id' => 'grid_' . strtolower(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_type' => strtoupper(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_data' => [
                                    'items' => $items->values()
                                ]
                            ];
                            $menuSummary['sub_menu'][] = $component;
                        }
                        $menuSummaries[] = $menuSummary;
                    }
                    
                }
                
                if (!empty($menuSummaries)) {
                    array_unshift($components, [
                        'component_id' => 'menu_summary_list',
                        'component_type' => 'MENU_SUMMARY_LIST',
                        'component_data' => $menuSummaries
                    ]);
                }
                
                return response()->json(['status'   => true,'role'=>$user->role->name,
                        'message' => "Home UI for {$user->name}",
                    'data' => [
                        
                        'screen_title' => "{$user->name}!",
                        'profile_url' => $user->profile_pic 
                            ? asset('storage/uploads/profile/' . $user->profile_pic) 
                            : asset('storage/uploads/profile/na.png') ,

                        'columns' => config('constants.SUB_MENU_COLUMNS'),
                        'components' => $menuSummaries
                    ],
                ]);
            
              }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    public function getSubMenuScreenDataV2(Request $request)
    {
        try{
            ApiLogService::info('Home request received', $request->all());
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:tbl_users,id',
                'parent_id' => 'required|integer'

            ]);
             $user = User::with('role.permissions')->find($request->user_id);
                if (!$user || !$user->role) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                $menuGrouped = $user->role->permissions->groupBy(fn($p) => $p->menu_id)->filter();
                $components = [];
                $menuSummaries = [];
                $processedMenus = [];
                foreach ($menuGrouped as $menuId => $permsInMenu) {
                        $allSubs = $permsInMenu->map(fn($p) => $p->submenus)->flatten(1)->filter()->unique('id');
                        $parentSubmenu = $allSubs->firstWhere('id', $request->parent_id);
                        $submenusForMenu = $allSubs->filter(fn($s) =>$s->parent_id == $request->parent_id)->sortBy('order')->values();
                        if ($submenusForMenu->isEmpty()) continue;
                        $submenuCount = $submenusForMenu->count();
                        $menuSummary = ['title' => $parentSubmenu->name,'order'=> $parentSubmenu->order,'count' => $submenuCount,'sub_menu' => []];
                        $grouped = $submenusForMenu->groupBy(fn($s) => optional($s->submenutype)->id);
                        foreach ($grouped as $typeId => $grp) {
                            $typeName = optional($grp->first()->submenutype)->name ?: 'Other';
                            $items = $grp->map(function ($s) {
                                return [
                                    'item_id' => "submenu_{$s->id}",
                                    'title' => $s->name,
                                    'order'=> $s->order,
                                    'image_url' => asset("storage/uploads/sub-menu/{$s->image}"),
                                    'background_color_hex' => $s->color_code,
                                    'action' => [
                                        'type' => 'NAVIGATE',
                                        'payload' => [
                                            'route' =>  $s->action ?? ''
                                        ]
                                    ]
                                ];
                            });

                            $component = [
                                'component_id' => 'grid_' . strtolower(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_type' => strtoupper(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                                'component_data' => [
                                    'items' => $items->values()
                                ]
                            ];
                            $menuSummary['sub_menu'][] = $component;
                        }
                        $menuSummaries[] = $menuSummary;
                }
                
                if (!empty($menuSummaries)) {
                    array_unshift($components, [
                        'component_id' => 'menu_summary_list',
                        'component_type' => 'MENU_SUMMARY_LIST',
                        'component_data' => $menuSummaries
                    ]);
                }
                return response()->json(['status'   => true,'role'=>$user->role->name,
                        'message' => "child menu list Found",
                    'data' => [
                        
                        'screen_title' => "Hey, {$user->name}!",
                        'columns' => config('constants.SUB_MENU_COLUMNS'),
                        'components' => $menuSummaries
                    ],
                    
                ]);

        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
       
    }
  

public function loggedInUserProfile(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthorized User!!'
        ], 401); 
    }

    try {
        $user = auth()->user(); 
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404); 
        }

        if ($user->status == 1) {
            $data = User::with('role:id,name', 'state:id,name', 'city:id,name') 
                ->where('id', $user->id)
                ->select(
                    'name',
                    'email',
                    'mobile',
                    'staff_id',
                    'role_id', 
                    'joining_date',
                    'status',
                    'profile_pic',
                    'addhar_front_pic',
                    'addhar_back_pic',
                    'state_id',
                    'city_id'  
                )
                ->first();

            $data->role_name  = $data->role ? $data->role->name : null;
            $data->state_name = $data->state ? $data->state->name : null;
            $data->city_name  = $data->city ? $data->city->name : null;
            $data->status_text = $this->getStatusText($data->status);

            unset($data->role, $data->state, $data->city, $data->role_id, $data->state_id, $data->city_id);
            unset( $data->status);
           
            $data->profile_pic       = $data->profile_pic ? asset('storage/uploads/profile/' . $data->profile_pic): asset('storage/uploads/profile/na.png');
            $data->addhar_front_pic  = $data->addhar_front_pic ? asset('storage/uploads/aadhar/' . $data->addhar_front_pic): asset('storage/uploads/profile/na.png');
            $data->addhar_back_pic   = $data->addhar_back_pic ? asset('storage/uploads/aadhar/' . $data->addhar_back_pic): asset('storage/uploads/profile/na.png');
            
            return response()->json([
                'status'  => true,
                'message' => "User Found",
                'data'    => $data
            ], 200);
        } 
        elseif ($user->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Inactive User',
                'status_text' => 'Inactive'
            ], 403); 
        } 
        elseif ($user->status == 3) {
            return response()->json([
                'status' => false,
                'message' => 'User account has been deleted',
                'status_text' => 'Deleted'
            ], 410); 
        }
        else {
            return response()->json([
                'status' => false,
                'message' => 'User not found!!'
            ], 404); 
        }
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => $e->getMessage()
        ], 500); 
    }
}

private function getStatusText($status)
{
    switch ($status) {
        case 1:
            return 'Active';
        case 0:
            return 'Inactive';
        case 3:
            return 'Deleted';
        default:
            return 'Unknown';
    }
}
    
}
