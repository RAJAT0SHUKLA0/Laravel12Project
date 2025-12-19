<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginUserController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\LeaveController;
use App\Http\Controllers\API\RegularizationController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\TeamsController;
use App\Http\Controllers\API\MasterController;
use App\Http\Controllers\API\SellerController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\AddCartController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\BillController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\HomeController2;
use App\Http\Controllers\API\HistoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
   Route::post('login-user', [LoginUserController::class, 'index'])->middleware('reject.query.string');
   Route::post('login-rider', [LoginUserController::class, 'LoginByRider'])->middleware('reject.query.string');
    Route::get('/check-protocol', function (Request $request) {
    return response()->json([
        'protocol' => $request->server('SERVER_PROTOCOL'),
        'ok' => true,
    ]);
});
Route::middleware(['reject.query.string','check.sanctum','check.user.status'])->group(function () {
    Route::post('mark-attendance', [AttendanceController::class, 'markAttendance']);
    Route::post('clear-attendance', [AttendanceController::class, 'clearAttendance']);
    Route::post('attendance-list', [AttendanceController::class, 'attendanceList']);
    Route::post('check-attendance-status', [AttendanceController::class, 'checkAttendanceStatus']);
    
    Route::post('add-leave', [LeaveController::class, 'addLeaves']);
    Route::post('leave-list', [LeaveController::class, 'LeaveList']);
    Route::post('update-leave-status', [LeaveController::class, 'LeaveStatusUpdate']);
    Route::post('leave-team-wise-list', [LeaveController::class, 'LeaveTeamWiseList']);
    
    
    Route::post('add-expense', [ExpenseController::class, 'addExpense']);
    Route::post('expense-list', [ExpenseController::class, 'expenseList']);
    Route::post('all-expenses-list', [ExpenseController::class, 'allExpenseList']);
    Route::post('update-expense-status', [ExpenseController::class, 'expenseStatusUpdate']);
    
    Route::post('add-regularize', [RegularizationController::class, 'addRegularize']);
    Route::post('regularize-list', [RegularizationController::class, 'regularizeList']);
    Route::post('update-regularize-status', [RegularizationController::class, 'RegularizeStatusUpdate']);
    Route::post('regularize-team-wise-list', [RegularizationController::class, 'regularizeTeamWiseList']);

    
    Route::post('add-location', [LocationController::class, 'getUserLocation']);
    Route::post('location-config', [LocationController::class, 'loadLocationConfig']);

    
    Route::post('add-staff', [StaffController::class, 'AddStaff']);
    Route::post('staff-list', [StaffController::class, 'staffList']);
    Route::post('staff-detail', [StaffController::class, 'staffDetail']);
    Route::post('update-staff', [StaffController::class, 'UpdateStaff']);
    Route::post('update-staff-status', [StaffController::class, 'staffStatusUpdate']);
    
    Route::post('team-wise-list', [TeamsController::class, 'teamWiseList']);
    Route::post('team-wise-attendance-list', [TeamsController::class, 'teamWiseAttendanceList']);
    Route::post('team-wise-location-list', [TeamsController::class, 'teamWiseLocationList']);
    
    Route::post('get-state', [MasterController::class, 'getState']);
    Route::post('get-city', [MasterController::class, 'getCity']);
    Route::post('get-beat', [MasterController::class, 'getBeatCityWise']);
    Route::post('get-role', [MasterController::class, 'getRole']);
    Route::post('get-all-data', [MasterController::class, 'getMasterData']);
    Route::post('get-leave-type', [MasterController::class, 'getLeaveType']);
    Route::post('get-seller-type', [MasterController::class, 'getSellerType']);
    Route::post('delete-request', [MasterController::class, 'deleteRequest']);
    Route::post('get-all-beat', [MasterController::class, 'getBeat']);
    Route::post('get-beat-assign-order-wise', [MasterController::class, 'getBeatAssignOrderWise']);
    Route::post('get-rider-list', [MasterController::class, 'getRider']);
    
    
    
    Route::post('get-category', [CategoryController::class, 'getCategory']);
    Route::post('get-varient', [CategoryController::class, 'getVarient']);
    
    
    Route::post('home', [HomeController::class, 'getHomeScreenData']);
    Route::post('sub-menu', [HomeController::class, 'getSubMenuScreenData']);
   
    Route::prefix('V2')->group(function () {
       Route::post('home', [HomeController::class, 'initiateHome']);
    });


    Route::prefix('V2')->group(function () {
       Route::post('home2', [HomeController2::class, 'initiateHomeV2']);
       Route::post('sub-menu2', [HomeController::class, 'getSubMenuScreenDataV2']);
    });
    






    Route::post('logout', [LoginUserController::class, 'logout']);
    Route::post('add-seller', [SellerController::class, 'AddSeller']);
    Route::post('seller-list', [SellerController::class, 'SellerList']);
    Route::post('seller-detail', [SellerController::class, 'SellerDetail']);
    Route::post('update-seller', [SellerController::class, 'UpdateSeller']);
    Route::post('update-seller-status', [SellerController::class, 'SellerStatusUpdate']);
    Route::post('seller-list-beat-wise', [SellerController::class, 'SellerListBeatWise']);
    Route::post('seller-Profile', [SellerController::class, 'SellerProfile']);

    
    Route::post('product', [ProductController::class, 'ProductList']);
    Route::post('product-save', [ProductController::class, 'addProduct']);
    Route::post('product-update', [ProductController::class, 'updateProduct']);
    Route::post('update-product-status', [ProductController::class, 'ProductStatusUpdate']);
    
    
    
    Route::post('product-search-wise-list', [AddCartController::class, 'productCategoryWiseList']);
    Route::post('add-cart', [AddCartController::class, 'addCart']);
    Route::post('cart-list', [AddCartController::class, 'cartList']);
    Route::post('order', [OrderController::class, 'createOrder']);
    Route::post('order-list', [OrderController::class, 'OrderList']);
    Route::post('invoice/{orderId}', [OrderController::class, 'downloadPdf'])->name('downloadPdf');
    Route::post('order-assign-by-rider-list', [OrderController::class, 'orderAssignByRiderList']);
    Route::post('get-assign-order-beat-wise-list', [OrderController::class, 'getAssignOrderBeatWise']);
    Route::post('order-assign-save', [OrderController::class, 'orderAssignSave']);
    Route::post('order-status-update', [OrderController::class, 'markAsPickup']);
    Route::post('order-start-delivery', [OrderController::class, 'startDeliveryOrder']);
    Route::post('order-delivery', [OrderController::class, 'deliverOrder']);
    Route::post('add-product-special-price', [AddCartController::class, 'addProductSpecialPrice']);

    Route::post('payment-save', [BillController::class, 'addPayment']);
    Route::post('payment-settlement', [BillController::class, 'billSettlement']);
    Route::post('payment-bill-list', [BillController::class, 'billList']);
    Route::post('payment-bill-list-by-rider', [BillController::class, 'billListByRider']);
    Route::post('payment-settlement-by-rider', [BillController::class, 'billSettlementByRider']);
    Route::post('rider-pending-bills', [BillController::class, 'riderPendingBills']);
    Route::post('rider-bill-settlement-approve', [BillController::class, 'riderBillSettlementApprove']);
    
    Route::post('order-history', [HistoryController::class, 'orderHistory']);
    Route::post('transaction-history', [HistoryController::class, 'transactionHistory']);

    Route::post('logged-in-user-profile', [HomeController::class, 'loggedInUserProfile']);





    Route::post('brand-list', [BrandController::class, 'brandList']);






    Route::post('welcome-notification', [FcmController::class, 'sendWelcomeMessage']);






});
    

    
