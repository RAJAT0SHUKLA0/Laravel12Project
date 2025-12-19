<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Utils\Uploads;
use App\Models\Brand;
use App\Helper\Message;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\DB;
use App\Services\ApiLogService;

class BrandController extends Controller
{
    public function brandList(Request $request)
    {
        try {
            ApiLogService::info('Brand request received', $request->all());
            $brandList = Brand::where('is_delete', '!=', 1)->where('status', 1)->select('id','name','image')->get()->map(function ($brand) {
        $brand->image = asset('storage/uploads/brand/' . $brand->image);
        return $brand;
    });

            if (!empty($brandList) && $brandList->count() > 0) {
                ApiLogService::success(Message::BRAND_SUCCESS_LIST, $brandList);
                return ApiResponseService::success(Message::BRAND_SUCCESS_LIST, $brandList);
            } else {
                ApiLogService::success(Message::BRAND_UNSUCCESS_LIST, []);
                return ApiResponseService::success(Message::BRAND_UNSUCCESS_LIST, []);
            }
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
}
