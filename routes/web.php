<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\auth\AuthController;
use App\Http\Controllers\admin\city\CityController;
use App\Http\Controllers\admin\state\StateController;
use App\Http\Controllers\admin\area\AreaController;
use App\Http\Controllers\admin\leaveType\LeaveTypeController;
use App\Http\Controllers\admin\leave\LeaveController;
use App\Http\Controllers\admin\attendance\AttendanceController;
use App\Http\Controllers\admin\regularize\RegularizeController;
use App\Http\Controllers\admin\location\LocationController;
use App\Http\Controllers\admin\staff\StaffController;
use App\Http\Controllers\admin\seller\SellerController;
use App\Http\Controllers\admin\sellerType\SellerTypeController;
use App\Http\Controllers\admin\setting\MenuController;
use App\Http\Controllers\admin\setting\SubMenuTypeController;
use App\Http\Controllers\admin\setting\SubMenuController;
use App\Http\Controllers\admin\varient\VarientController;
use App\Http\Controllers\admin\category\CategoryController;
use App\Http\Controllers\admin\subcategory\SubCategoryController;
use App\Http\Controllers\admin\permission\PermissionController;
use App\Http\Controllers\admin\product\ProductController;
use App\Http\Controllers\admin\brand\BrandController;
use App\Http\Controllers\admin\order\OrderContoller;
use App\Http\Controllers\admin\bill\BillController;
use App\Http\Controllers\admin\expense\ExpenseController;
use App\Http\Controllers\admin\report\ReportController;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;




    Route::post('/login',[AuthController::class,'login'])->name('loginSave');

    Route::middleware('auth.or.guest.admin')->group(function(){
    Route::get('/',[AuthController::class,'index'])->name('login');
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    ///// state Route /////
    Route::match(['GET','POST'],'/state',[StateController::class,'index'])->name('state');
    Route::post('/state-save', [StateController::class, 'stateSave'])->name('stateSave');
    Route::get('/state-edit/{id}', [StateController::class, 'editState'])->name('editState');
    Route::put('/state-update/{id}', [StateController::class, 'stateUpdate'])->name('stateUpdate');
    Route::get('/state-delete/{id}', [StateController::class, 'softDeleteState'])->name('softDeleteState');
    

    ///// city /////
    Route::match(['GET','POST'],'/city',[CityController::class,'index'])->name('city');
    Route::post('/city-save', [CityController::class, 'citySave'])->name('citySave');
    Route::get('/city-edit/{id}', [CityController::class, 'editCity'])->name('editCity');
    Route::put('/city-update/{id}', [CityController::class, 'cityUpdate'])->name('cityUpdate');
    Route::get('/city-delete/{id}', [CityController::class, 'softDeleteCity'])->name('softDeleteCity');

    ///// Area /////
    Route::match(['GET','POST'],'/area',[AreaController::class,'index'])->name('area');
    Route::post('/area-save', [AreaController::class, 'areaSave'])->name('areaSave');
    Route::get('/area-edit/{id}', [AreaController::class, 'editArea'])->name('editArea');
    Route::put('/area-update/{id}', [AreaController::class, 'areaUpdate'])->name('areaUpdate');
    Route::get('/area-delete/{id}', [AreaController::class, 'softDelete'])->name('softDelete');
    
    ////// staff ///////
    Route::match(['GET','POST'],'/staff', [StaffController::class, 'index'])->name('stafflist');
    Route::get('/staff-add', [StaffController::class, 'add'])->name('staffAdd');
    Route::post('/staff-save', [StaffController::class, 'staffSave'])->name('staffSave');
    Route::get('/staff-edit/{id}', [StaffController::class, 'edit'])->name('staffEdit');
    Route::put('/staff-update/{id}', [StaffController::class, 'staffupdate'])->name('staffUpdate');
    Route::get('/staff-delete/{id}', [StaffController::class, 'delete'])->name('staffDelete');
    Route::get('/staff-status/{id}/{status}', [StaffController::class, 'statusupdate'])->name('statusUpdate');
    Route::match(['GET','PUT'],'/staff-change-password/{id}', [StaffController::class, 'changePassword'])->name('changePassword');
    Route::post('ajax/get-city', [StaffController::class, 'getCity'])->name('getCity');
    Route::get('/location-enable/{id}/{status}',[StaffController::class,'isLocationEnable'])->name('isLocationEnable');

      
    ////// seller ///////
    Route::match(['GET','POST'],'/seller', [SellerController::class, 'index'])->name('sellerlist');
    Route::get('/seller-add', [SellerController::class, 'add'])->name('sellerAdd');
    Route::post('/seller-save', [SellerController::class, 'sellerSave'])->name('sellerSave');
    Route::get('/seller-edit/{id}', [SellerController::class, 'edit'])->name('sellerEdit');
    Route::put('/seller-update/{id}', [SellerController::class, 'sellerupdate'])->name('sellerupdate');
    Route::get('/seller-delete/{id}', [SellerController::class, 'delete'])->name('sellerDelete');
    Route::get('/seller-status/{id}/{status}', [SellerController::class, 'statusupdate'])->name('sellerstatusUpdate');
    Route::post('ajax/get-area', [SellerController::class, 'getArea'])->name('getArea');
    Route::get('/seller-profile/{id}', [SellerController::class, 'sellerprofile'])->name('sellerprofile');
    Route::get('/transaction-report/{sellerId}', [SellerController::class, 'transactionReportSellerWise'])->name('transactionReportSellerWise');



      
    ///// LeaveType Route /////
    Route::get('/leaveType', [LeaveTypeController::class, 'index'])->name('leaveType');
    Route::post('/leaveType-save', [LeaveTypeController::class, 'leaveTypeSave'])->name('leaveTypeSave');
    Route::get('/leaveType-edit/{id}', [LeaveTypeController::class, 'editLeaveType'])->name('editLeaveType');
    Route::put('/leaveType-update/{id}', [LeaveTypeController::class, 'leaveTypeUpdate'])->name('leaveTypeUpdate');
    Route::get('/leave-delete/{id}', [LeaveTypeController::class, 'softDeleteLeaveType'])->name('softDeleteLeaveType');
    
    
    ///// SellerType Route /////
    
    Route::match(['GET','POST'],'/sellerType',[SellerTypeController::class,'index'])->name('sellerType');
    Route::post('/sellertype-save', [SellerTypeController::class, 'sellerTypeSave'])->name('sellerTypeSave');
    Route::get('/sellertype-edit/{id}', [SellerTypeController::class, 'editSellerType'])->name('editSellerType');
    Route::put('/sellertype-update/{id}', [SellerTypeController::class, 'sellerTypeUpdate'])->name('sellerTypeUpdate');
    Route::get('/sellertype-delete/{id}', [SellerTypeController::class, 'softDeleteSellerType'])->name('softDeleteSellerType');
    
    
     ///// category Route /////
    Route::match(['GET','POST'],'/category',[CategoryController::class,'index'])->name('categorylist');
    Route::get('/category-add', [CategoryController::class, 'add'])->name('categoryAdd');
    Route::post('/category-save', [CategoryController::class, 'categorySave'])->name('categorySave');
    Route::get('/category-edit/{id}', [CategoryController::class, 'edit'])->name('categoryEdit');
    Route::put('/category-update/{id}', [CategoryController::class, 'categoryupdate'])->name('categoryUpdate');
    Route::get('/category-status/{id}/{status}', [CategoryController::class, 'statusupdate'])->name('categorystatusUpdate');
    Route::get('/category-delete/{id}', [CategoryController::class, 'delete'])->name('categoryDelete');
    
    
    
     ///// Subcategory Route /////
    Route::match(['GET','POST'],'/subcategory',[SubCategoryController::class,'index'])->name('subcategorylist');
    Route::get('/subcategory-add', [SubCategoryController::class, 'add'])->name('subcategoryAdd');
    Route::post('/subcategory-save', [SubCategoryController::class, 'subcategorySave'])->name('subcategorySave');
    Route::get('/subcategory-edit/{id}', [SubCategoryController::class, 'editSubcategory'])->name('editSubcategory');
    Route::put('/subcategory-update/{id}', [SubCategoryController::class, 'SubcategoryUpdate'])->name('SubcategoryUpdate');
    Route::get('/subcategory-delete/{id}', [SubCategoryController::class, 'softDeleteSubcategory'])->name('softDeleteSubcategory');
    Route::get('/subcategory-status/{id}/{status}', [SubCategoryController::class, 'statusupdate'])->name('subcategorystatusUpdate');
    
    
    
    
      ///// Brand Route /////
    Route::match(['GET','POST'],'/brand',[BrandController::class,'index'])->name('brandlist');
     Route::get('/brand-add', [BrandController::class, 'add'])->name('brandAdd');
     Route::post('/brand-save', [BrandController::class, 'brandSave'])->name('brandSave');
    Route::get('/brand-edit/{id}', [BrandController::class, 'edit'])->name('brandEdit');
     Route::put('/brand-update/{id}', [BrandController::class, 'brandupdate'])->name('brandupdate');
    Route::get('/brand-status/{id}/{status}', [BrandController::class, 'statusupdate'])->name('brandstatusUpdate');
    Route::get('/brand-delete/{id}', [BrandController::class, 'delete'])->name('brandDelete');
    
    
    
    
     ///// Varient Route /////
    Route::match(['GET','POST'],'/varient',[VarientController::class,'index'])->name('varientlist');
    Route::post('/varient-save', [VarientController::class, 'varientSave'])->name('varientSave');
    Route::get('/varient-edit/{id}', [VarientController::class, 'editvarient'])->name('editvarient');
    Route::put('/varient-update/{id}', [VarientController::class, 'VarientUpdate'])->name('VarientUpdate');
    Route::get('/varient-delete/{id}', [VarientController::class, 'softDeleteVarient'])->name('softDeleteVarient');
    Route::get('/varient-status/{id}/{status}', [VarientController::class, 'statusupdate'])->name('varientstatusUpdate');
    
   
    

   
     ///// LeaveRoute /////
    Route::match(['GET','POST'],'/leave',[LeaveController::class,'index'])->name('leavelist');
    Route::get('/leave-status/{id}/{status}', [LeaveController::class, 'leavestatusupdate'])->name('leavestatusupdate');
    
         ///// ExpenseRoute /////
    Route::match(['GET','POST'],'/expense',[ExpenseController::class,'index'])->name('expenselist');
    Route::get('/expense-status/{id}/{status}', [ExpenseController::class, 'expensestatusupdate'])->name('expensestatusupdate');
    
    Route::match(['GET','POST'],'/attendance',[AttendanceController::class,'index'])->name('attendanceList');
    Route::get('/attendance-status/{id}/{status}', [AttendanceController::class, 'statusupdate'])->name('attendanceStatusUpdate');
    
      ///// RegularizeRoute /////
    Route::match(['GET','POST'],'/regularize',[RegularizeController::class,'index'])->name('regularizelist');
    Route::get('/regularize-status/{id}/{status}', [RegularizeController::class, 'regularizeStatusUpdate'])->name('regularizeStatusUpdate');
    
     ///// location /////
    Route::match(['GET','POST'],'/location',[LocationController::class,'index'])->name('locationList');
    Route::get('/location-ajax-list',[LocationController::class,'renderLocationList'])->name('renderLocationList');
    
    
    /// settings ////
    Route::match(['GET','POST','PUT'],'/menu/{id?}',[MenuController::class,'index'])->name('Menu');
    Route::get('/menu-edit/{id}', [MenuController::class, 'edit'])->name('editMenu');
    Route::get('/menu-delete/{id}', [MenuController::class, 'delete'])->name('DeleteMenu');
    
    Route::match(['GET','POST','PUT'],'/sub-menu-type/{id?}',[SubMenuTypeController::class,'index'])->name('SubMenuType');
    Route::get('/sub-menu-type-edit/{id}', [SubMenuTypeController::class, 'edit'])->name('editSubMenuType');
    Route::get('/sub-menu-type-delete/{id}', [SubMenuTypeController::class, 'delete'])->name('DeleteSubMenuType');
    
    Route::match(['GET','POST','PUT'],'/sub-menu/{id?}',[SubMenuController::class,'index'])->name('SubMenu');
    Route::get('/sub-menu-edit/{id}', [SubMenuController::class, 'edit'])->name('editSubMenu');
    Route::get('/sub-menu-delete/{id}', [SubMenuController::class, 'delete'])->name('DeleteSubMenu');
    
    Route::match(['GET','POST','PUT'],'/permission/{id?}',[PermissionController::class,'index'])->name('Permission');
    Route::get('/permission-edit/{id}', [PermissionController::class, 'edit'])->name('edit');
    
    
    
    ///// Product Route //////////
    Route::match(['GET','POST'],'/product',[ProductController::class,'index'])->name('Product');
    Route::get('/product-add', [ProductController::class, 'add'])->name('ProductAdd');
    Route::post('/product-save',[ProductController::class,'save'])->name('ProductSave');
    Route::get('/product-edit/{id}', [ProductController::class, 'edit'])->name('ProductEdit');
    Route::put('/product-update/{id}', [ProductController::class, 'update'])->name('ProductUpdate');
    Route::get('/product-delete/{id}', [ProductController::class, 'delete'])->name('ProductDelete');
    Route::get('/product-status/{id}/{status}', [ProductController::class, 'statusupdate'])->name('ProductstatusUpdate');
    Route::post('/get-sub-category',[ProductController::class,'getSubcategory'])->name('getSubcategory');
    Route::post('/get-product-multi-section',[ProductController::class,'getMultiVarientSection'])->name('getMultiVarientSection');
    Route::get('/delete-this-varient/{id}', [ProductController::class, 'deleteThisVarient'])->name('deleteThisVarient');
    
    
    
    
    //////// Order Route //////////////
    Route::match(['GET','POST'],'/order',[OrderContoller::class,'index'])->name('order');
    Route::get('/order-details/{id}',[OrderContoller::class,'orderDetails'])->name('orderDetails');
    Route::get('/order-status/{id}/{status}', [OrderContoller::class, 'status'])->name('orderStatus');
    Route::match(['GET','POST'],'/order-assign',[OrderContoller::class,'orderAssign'])->name('orderAssign');
    Route::post('order-assign/save', [OrderContoller::class, 'orderAssignSave'])->name('orderAssignSave');
    Route::get('/cancel-order/{id}',[OrderContoller::class,'cancelOrder'])->name('cancelOrder');
    
    // new route added for invoice generation 
    Route::get('/generate-order-detail-invoice/{id}',[OrderContoller::class,'generate_order_detail_invoice'])->name('generate-order-detail-invoice');

    
    //////// Bill Settlement Route //////////////
    Route::post('/bill-settlement',[BillController::class,'billSettlement'])->name('billSettlement');
    Route::match(['GET','POST'],'/bill',[BillController::class,'index'])->name('billList');
    Route::post('/bill-save',[BillController::class,'saveCheque'])->name('saveCheque');
    Route::get('/bill-status/{id}/{status}',[BillController::class,'approveCheque'])->name('approveCheque');
    
        ////////Rider  Bill Settlement Route //////////////
    Route::match(['GET','POST'],'/rider-bill-settlements',[BillController::class,'riderBillList'])->name('riderBillList');
    Route::get('/rider-bill-status/{id}/{status}',[BillController::class,'approveriderBill'])->name('approveriderBill');


   ///// report Route /////
    Route::match(['GET','POST'],'/bill-transaction-report',[ReportController::class,'billTransactionReport'])->name('billTransactionReport');
    Route::match(['GET','POST'],'/order-report',[ReportController::class,'orderReport'])->name('orderReport');
    Route::get('/bill-transaction-history/{id}',[ReportController::class,'billTransactionHistory'])->name('billTransactionHistory');
    Route::get('/order-detail-report/{id}',[ReportController::class,'orderDetailReport'])->name('orderDetailReport');
    Route::get('/export-order-report', function () {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    })->name('exportOrderReport');


    
      
      



});
