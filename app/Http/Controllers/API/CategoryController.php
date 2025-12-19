<?php

namespace App\Http\Controllers\API;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Helper\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Varient;


class CategoryController extends Controller
{
      public function getCategory()
    {
        try {
            $data = Category::where('is_delete','!=',1)->select('id', 'name')
                ->with([
                    'subcategories' => function ($query) {
                        $query->select('id', 'name', 'category_id');
                    }
                ])
                ->orderBy('name', 'asc')
                ->get();
    
            ApiLogService::success(sprintf(Message::MASTER_SUCCESS, 'category-subcategory'), $data);
    
            return ApiResponseService::success(
                sprintf(Message::MASTER_SUCCESS, 'category, subcategory'),
                $data
            );
    
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
   public function getVarient()
{
    try {
        $data = Varient::where('is_delete', '!=', 1)
            ->select('id', 'name', 'unit_id')
            ->with(['unit'])
            ->orderBy('name', 'asc')
            ->get();
            
            
            
               
        $data->each(function ($item) {
            $item->fullName = $item->fullName;
            unset($item->unit);// triggers accessor
        });

        ApiLogService::success(sprintf(Message::MASTER_SUCCESS, 'varient-unit'), $data);

        return ApiResponseService::success(
            sprintf(Message::MASTER_SUCCESS, 'varient, unit'),
            $data
        );
    } catch (\Exception $e) {
        ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
        return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
    }
}

}
