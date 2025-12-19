<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\State\StateRepository;
use App\Repositories\State\StateRepositoryInterface;
use App\Repositories\Area\AreaRepository;
use App\Repositories\Area\AreaRepositoryInterface;
use App\Repositories\City\CityRepository;
use App\Repositories\City\CityRepositoryInterface;
use App\Repositories\Staff\StaffRepository;
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Repositories\LeaveType\LeaveTypeRepository;
use App\Repositories\LeaveType\LeaveTypeRepositoryInterface;
use App\Repositories\Leave\LeaveRepository;
use App\Repositories\Leave\LeaveRepositoryInterface;
use App\Repositories\Attendance\AttendanceRepository;
use App\Repositories\Attendance\AttendanceRepositoryInterface;
use App\Repositories\Location\LocationRepository;
use App\Repositories\Location\LocationRepositoryInterface;
use App\Repositories\Regularize\RegularizeRepository;
use App\Repositories\Regularize\RegularizeRepositoryInterface;
use App\Repositories\Seller\SellerRepository;
use App\Repositories\Seller\SellerRepositoryInterface;
use App\Repositories\SellerType\SellerTypeRepository;
use App\Repositories\SellerType\SellerTypeRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Subcategory\SubCategoryRepository;
use App\Repositories\Subcategory\SubCategoryRepositoryInterface;
use App\Repositories\Varient\VarientRepository;
use App\Repositories\Varient\VarientRepositoryInterface;
use App\Repositories\Settings\MenuRepository;
use App\Repositories\Settings\MenuRepositoryInterface;
use App\Repositories\Settings\SubMenuTypeRepository;
use App\Repositories\Settings\SubMenuTypeRepositoryInterface;
use App\Repositories\Settings\SubMenuRepository;
use App\Repositories\Settings\SubMenuRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Brand\BrandRepositoryInterface;
use App\Repositories\Brand\BrandRepository;
use App\Repositories\Bill\BillRepositoryInterface;
use App\Repositories\Bill\BillRepository;
use App\Repositories\Expense\ExpenseRepository;
use App\Repositories\Expense\ExpenseRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StateRepositoryInterface::class, StateRepository::class);
        $this->app->bind(CityRepositoryInterface::class,CityRepository::class);
        $this->app->bind(AreaRepositoryInterface::class,AreaRepository::class);
        $this->app->bind(StaffRepositoryInterface::class,StaffRepository::class);
        $this->app->bind(StaffRepositoryInterface::class,StaffRepository::class);
        $this->app->bind(LeaveTypeRepositoryInterface::class,LeaveTypeRepository::class);
        $this->app->bind(LeaveRepositoryInterface::class,LeaveRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class,AttendanceRepository::class);
        $this->app->bind(RegularizeRepositoryInterface::class,RegularizeRepository::class);
        $this->app->bind(LocationRepositoryInterface::class,LocationRepository::class);
        $this->app->bind(SellerRepositoryInterface::class,SellerRepository::class);
        $this->app->bind(SellerTypeRepositoryInterface::class,SellerTypeRepository::class);
        $this->app->bind(MenuRepositoryInterface::class,MenuRepository::class);
        $this->app->bind(SubMenuTypeRepositoryInterface::class,SubMenuTypeRepository::class);
        $this->app->bind(SubMenuRepositoryInterface::class,SubMenuRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class,CategoryRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class,SubCategoryRepository::class);
        $this->app->bind(VarientRepositoryInterface::class,VarientRepository::class);
        $this->app->bind(ProductRepositoryInterface::class,ProductRepository::class);
        $this->app->bind(BrandRepositoryInterface::class,BrandRepository::class);
        $this->app->bind(BillRepositoryInterface::class,BillRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class,ExpenseRepository::class);



    }
}
