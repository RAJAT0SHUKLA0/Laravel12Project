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
use App\Models\BillSettlementDetail;
use App\Models\Transaction;

class HomeController2 extends Controller
{
    
public function initiateHomeV2(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
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
       
        // Update device ID if different
        if ($user->device_id !== $request->device_id) {
            $user->update(['device_id' => $request->device_id]);
            ApiLogService::info('Device ID updated', ['user_id' => $user->id, 'new_device_id' => $request->device_id]);
        }
 
        $today = Carbon::today();
        $isAdmin = auth()->user()->role_id == 1 || auth()->user()->role_id == 2;
       
        // Fetch all data in parallel using collections
        $attendanceData = $this->getAttendanceData($userId, $today);
        $leaveData = $this->getLeaveData($userId, $today, $isAdmin);
        $regularizeData = $this->getRegularizeData($userId, $today, $isAdmin);
        $expenseData = $this->getExpenseData($userId, $today, $isAdmin);
        $staffData = $this->getStaffData($today);
        $sellerData = $this->getSellerData();
        $productData = $this->getProductData();
        $orderData = $this->getOrderData($today,$userId,$isAdmin);
        $assignOrderData = $this->assignOrderData($today,$userId,$isAdmin);
        $riderOrders = $this->riderOrders($today,$userId,$isAdmin);
        $approveBills = $this->approveBills($today,$userId,$isAdmin);
        $trasactionHistoryCount = $this->trasactionHistoryCount($today,$userId,$isAdmin);
       
        $menuSummaries = $this->buildMenuSummaries($user, [
            'Mark Attendance' => $attendanceData,
            'View Leaves' => $leaveData,
            'Regularization Requests' => $regularizeData,
            'View Expenses' => $expenseData,
            'Staff Report' => $staffData,
            'Manage Employees' => array_merge($leaveData, $regularizeData, $expenseData),
            'Sellers' => $sellerData,
            'Products' => $productData,
            'Manage Orders' => $orderData,
            'Assign Order' => $assignOrderData,
            'Your Orders' => $riderOrders,
            'Approve Bills' => $approveBills,
            'Payment History' => $trasactionHistoryCount,
        ]);
       
        return response()->json([
            'status' => true,
            'role' => $user->role->name,
            'message' => "Home UI for {$user->name}",
            'data' => [
                'screen_title' => "{$user->name}!",
                'profile_url' => $user->profile_pic
                    ? asset('storage/uploads/profile/' . $user->profile_pic)
                    : asset('storage/uploads/profile/na.png'),
                'columns' => config('constants.SUB_MENU_COLUMNS'),
                'components' => $menuSummaries
            ],
        ]);
       
    } catch (\Exception $e) {
        return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
    }
}
 
private function getAttendanceData($userId, $today)
{
    $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
   
    return [
        'in_time' => $attendance->in_time ?? 'No data available',
        'out_time' => $attendance->out_time ?? 'No data available',
        'in_time_status' => !empty($attendance?->in_time),
        'out_time_status' => !empty($attendance?->out_time),
    ];
}
 
private function getLeaveData($userId, $today, $isAdmin)
{
    $leave = Leave::where('user_id', $userId)->whereDate('date', $today)->first();
    $leaveCount = Leave::when(!$isAdmin, fn($q) => $q->where('user_id', $userId))->count();
    $pendingLeaveCount = Leave::when(!$isAdmin, fn($q) => $q->where('user_id', $userId)->where('status',0))->count();
    $todayLeaveCount = Leave::whereDate('date', $today)->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))->count();
   
    $statusLabels = [0 => 'Pending', 1 => 'Approved', 2 => 'Rejected'];
   
    return [
        'status' => $statusLabels[optional($leave)->status] ?? '',
        'start_date' => $leave->start_date ?? '',
        'end_date' => $leave->end_date ?? '',
        'pendingLeaveCount' => $pendingLeaveCount,
        'totalLeaveCount' => $leaveCount,
        'todayLeaveCount' => $todayLeaveCount,
    ];
}
 
private function getRegularizeData($userId, $today, $isAdmin)
{
    $regularize = Regularize::where('user_id', $userId)->whereDate('date', $today)->first();
    $regularizeCount = Regularize::when(!$isAdmin, fn($q) => $q->where('user_id', $userId))->count();
    $pendingRegularizeCount = Regularize::when(!$isAdmin, fn($q) => $q->where('user_id', $userId)->where('status',1))->count();
    $todayRegularizeCount = Regularize::whereDate('date', $today)->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))->count();
   
    $statusLabels = [0 => 'Pending', 1 => 'Approved', 2 => 'Rejected'];
   
    return [
        'status' => $statusLabels[optional($regularize)->status] ?? '',
        'date' => $regularize->date ?? '',
        'remark' => $regularize->remark ?? '',
        'pendingRegularizeCount' => $pendingRegularizeCount,
        'totalRegulizeCount' => $regularizeCount,
        'todayRegulizeCount' => $todayRegularizeCount,
    ];
}
 
private function getExpenseData($userId, $today, $isAdmin)
{
    $expense = Expense::where('staff_id', $userId)->whereDate('expense_date', $today)->first();
    $expenseCount = Expense::when(!$isAdmin, fn($q) => $q->where('staff_id', $userId))->count();
    $pendingExpenseCount = Expense::when(!$isAdmin, fn($q) => $q->where('staff_id', $userId)->where('status',0))->count();
    $todayExpenseCount = Expense::whereDate('expense_date', $today)->when(!$isAdmin, fn($q) => $q->where('staff_id', $userId))->count();
   
    $statusLabels = [0 => 'Pending', 1 => 'Approved', 2 => 'Rejected'];
   
    return [
        'status' => $statusLabels[optional($expense)->status] ?? '',
        'date' => $expense->expense_date ?? '',
        'remark' => $expense->remark ?? '',
        'pendingExpenseCount' => $pendingExpenseCount,
        'totalExpenseCount' => $expenseCount,
        'todayExpenseCount' => $todayExpenseCount,
    ];
}
 
private function getStaffData($today)
{
    $allStaff = User::where('status', 1)->where('role_id', '!=', 1)->select('id', 'name', 'email', 'mobile')->get();
    $presentStaffIds = Attendance::whereDate('date', $today)->pluck('user_id')->toArray();
   
    return [
        'totalStaff' => $allStaff->count(),
        'presentStaff' => $allStaff->whereIn('id', $presentStaffIds)->count(),
        'absentStaff' => $allStaff->whereNotIn('id', $presentStaffIds)->count(),
    ];
}
 
private function getSellerData()
{
    return [
        'totalSellerCount' => Seller::where('status', '!=', 3)->count(),
        'activeSellerCount' => Seller::where('status', 1)->count(),
        'inactiveSellerCount' => Seller::where('status', 0)->count(),
    ];
}
 
private function getProductData()
{
    $topSellingProducts = OrderDetail::with('product')
        ->select('product_id', DB::raw('COUNT(product_id) as total_sold'))
        ->groupBy('product_id')
        ->orderByDesc('total_sold')
        ->get()
        ->pluck('total_sold', 'product.name')  
        ->toArray();
       
    return [
        'allProducts' => Product::where('status', '!=', 3)->where('is_delete', 0)->count(),
        'activeProducts' => Product::where('status', 1)->where('is_delete', 0)->count(),
        'inactiveProducts' => Product::where('status', 0)->where('is_delete', 0)->count(),
        'topSellingProducts' => $topSellingProducts,
    ];
}




private function getOrderData($today, $userId, $isAdmin)
{
    $sevenDaysAgo = Carbon::today()->subDays(7);
    $thirtyDaysAgo = Carbon::today()->subDays(30);
    
    $baseQuery = Order::when(!$isAdmin, fn($q) => $q->where('staff_id', $userId));

    return [
        'orderCounts' => [
            'all'     => (clone $baseQuery)->count(),
            'today'   => (clone $baseQuery)->whereDate('date', $today)->count(),
            'last_7'  => (clone $baseQuery)->whereBetween('date', [$sevenDaysAgo, $today])->count(),
            'last_30' => (clone $baseQuery)->whereBetween('date', [$thirtyDaysAgo, $today])->count(),
        ],

        'todayOrdersByStatus' => [
            'pending'    => (clone $baseQuery)->whereDate('date', $today)->where('status', 0)->count(),
            'to_deliver' => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 1)->count(),
            'delivered'  => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 3)->count(),
            'cancelled'  => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 4)->count(),
            'returned'   => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 5)->count(),
            'assigned'   => (clone $baseQuery)->whereDate('order_assign_date', $today)->where('status', 6)->count(),
            'pickup'     => (clone $baseQuery)->whereDate('pickup_date', $today)->count(),
        ],

        'allOrdersByStatus' => [
            'pending'    => (clone $baseQuery)->where('status', 0)->count(),
            'to_deliver' => (clone $baseQuery)->where('status', 1)->count(),
            'delivered'  => (clone $baseQuery)->where('status', 3)->count(),
            'cancelled'  => (clone $baseQuery)->where('status', 4)->count(),
            'returned'   => (clone $baseQuery)->where('status', 5)->count(),
            'assigned'   => (clone $baseQuery)->where('status', 6)->count(),
        ],
    ];
}


private function assignOrderData($today, $userId, $isAdmin)
{
    $baseQuery = Order::when(!$isAdmin, fn($q) => $q->where('staff_id', $userId));

    return [
        'assignOrderCounts' => [
            'allPendingOrderCount'   => (clone $baseQuery)->where('status', 0)->count(),
            'todayPendingOrderCount' => (clone $baseQuery)->where('status', 0)->whereDate('date', $today)->count(),
        ],
    ];
}

private function approveBills($userId, $isAdmin)
{
    $baseQuery = BillSettlementDetail::when(!$isAdmin, function ($q) use ($userId) {
        return $q->where('staff_id', $userId);
    });

    return [
        'Bills' => [
            'allApprovedBills' => (clone $baseQuery)->where('status', 1)->count(),
            'allPendingBills'  => (clone $baseQuery)->where('status', 0)->count(),
        ],
    ];
}

private function trasactionHistoryCount($today, $userId, $isAdmin)
{
    $baseQuery = Transaction::when(!$isAdmin, function ($q) use ($userId) {
        return $q->where('staff_id', $userId);
    });

    $paymentModes = [
        1 => 'Cash',
        2 => 'Cheque',
        3 => 'UPI',
    ];

    $statusLabels = [
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Rejected',
    ];

    // Group by status and payment_mode
    $counts = $baseQuery->select('status', 'payment_mode', DB::raw('COUNT(*) as total'))
        ->groupBy('status', 'payment_mode')
        ->get();

    // Format response
    $formatted = [];
    foreach ($counts as $row) {
        $status = $statusLabels[$row->status] ?? 'Unknown';
        $mode   = $paymentModes[$row->payment_mode] ?? 'Other';

        if (!isset($formatted[$status])) {
            $formatted[$status] = [
                'Cash'   => 0,
                'Cheque' => 0,
                'UPI'    => 0,
            ];
        }
        $formatted[$status][$mode] = $row->total;
    }

    return [
        'Transactions' => $formatted
    ];
}


private function riderOrders($today, $userId, $isAdmin)
{
    $sevenDaysAgo   = Carbon::today()->subDays(7);
    $thirtyDaysAgo  = Carbon::today()->subDays(30);

    $assignQuery = OrderAssign::query();

    if (!$isAdmin) {
        $assignQuery->where('rider_id', $userId);
    }

    $assignedOrders = $assignQuery->pluck('order_id')->toArray();
    $orderIds = collect($assignedOrders)
        ->flatMap(fn($ids) => explode(',', $ids))
        ->filter()
        ->map(fn($id) => (int) $id)
        ->unique()
        ->values()
        ->toArray();

    $baseQuery = Order::whereIn('id', $orderIds);

    return [
        'orderCounts' => [
            'all'     => (clone $baseQuery)->count(),
            'today'   => (clone $baseQuery)->whereDate('date', $today)->count(),
            'last_7'  => (clone $baseQuery)->whereBetween('date', [$sevenDaysAgo, $today])->count(),
            'last_30' => (clone $baseQuery)->whereBetween('date', [$thirtyDaysAgo, $today])->count(),
        ],

        'todayOrdersByStatus' => [
            'pending'    => (clone $baseQuery)->whereDate('date', $today)->where('status', 0)->count(),
            'to_deliver' => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 1)->count(),
            'delivered'  => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 3)->count(),
            'cancelled'  => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 4)->count(),
            'returned'   => (clone $baseQuery)->whereDate('delivery_date', $today)->where('status', 5)->count(),
            'assigned'   => (clone $baseQuery)->whereDate('order_assign_date', $today)->where('status', 6)->count(),
            'pickup'     => (clone $baseQuery)->whereDate('pickup_date', $today)->count(),
        ],

        'allOrdersByStatus' => [
            'delivered'  => (clone $baseQuery)->where('status', 3)->count(),
        ],
    ];
}




 
private function buildMenuSummaries($user, $menuMapping)
{
    $menuGrouped = $user->role->permissions->groupBy(fn($p) => $p->menu_id)->filter();
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
           
            $menuSummary = [
                'title' => $menu->name,
                'order' => $menu->orderby,
                'count' => $submenusForMenu->count(),
                'sub_menu' => []
            ];
           
            $grouped = $submenusForMenu->groupBy(fn($s) => optional($s->submenutype)->id);
           
            foreach ($grouped as $typeId => $grp) {
                $typeName = optional($grp->first()->submenutype)->name ?: 'Other';
               
                $items = $grp->map(function ($s) use ($menuMapping) {
                    $dataList = $menuMapping[$s->name] ?? [];
                    // $isShow = $s->name === 'Mark Attendance' || !collect($dataList)->every(function ($v) {
                    //     return $v === null || $v === '' || $v === 0 || $v === false || $v === 'No data available';
                    // });
                   
                    return [
                        'parent_id' => $s->id,
                        'item_id' => "submenu_{$s->id}",
                        'title' => $s->name,
                        'order' => $s->order,
                        'image_url' => asset("storage/uploads/sub-menu/{$s->image}"),
                        'background_color_hex' => $s->color_code,
                        'action' => [
                            'type' => 'NAVIGATE',
                            'payload' => ['route' => $s->action ?? '']
                        ],
                        // 'isshow' => $isShow,
                        'data' => $dataList
                    ];
                });
 
                $menuSummary['sub_menu'][] = [
                    'component_id' => 'grid_' . strtolower(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                    'component_type' => strtoupper(preg_replace('/[^a-z0-9_]/', '_', $typeName)),
                    'component_data' => ['items' => $items->values()]
                ];
            }
            $menuSummaries[] = $menuSummary;
        }
    }
   
    return $menuSummaries;
}
 
 
    
}
