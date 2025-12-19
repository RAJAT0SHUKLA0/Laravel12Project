<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Varient;
use App\Models\Unit;
use App\Models\Cart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\JsonParserService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SellerOrderPrice;

class AddCartController extends Controller
{
    private $jsonParse;
    public function __construct(JsonParserService $JsonParserService)
    {
        $this->jsonParse = $JsonParserService;
    }

    public function productCategoryWiseList(Request $request)
    {
        try {
            ApiLogService::info("product request received", $request->all());
            $discountPercentage = 0;
            $query = Product::Where("status", 1)
                ->with([
                    "category" => function ($q) {
                        $q->select("id", "name");
                    },
                    "getdetail.varient.unit",
                ])
                ->orderBy("id", "desc");
                
                 if ($request->has("brand_id") && !empty($request->brand_id)) {
                    $query->where("brand_id", $request->brand_id);
                }
                
            if ($request->has("name") && !empty($request->name)) {
                $name = $request->name;
                $products = (clone $query)
                    ->where("name", "like", "%{$name}%")
                    ->get();
                if ($products->isEmpty()) {
                    $products = (clone $query)
                        ->whereHas("category", function ($q) use ($name) {
                            $q->where("name", "like", "%{$name}%");
                        })
                        ->get();
                }
            } else {
                $products = $query->get();
            }
            $data = $products->map(function ($product) use ($discountPercentage,$request) {
              
                return [  
                    "id" => $product->id,
                    "name" => $product->name,
                    "price" => $product->price,
                    "hsn_code" => $product->hsn_code,
                    "status" => $product->status == 1 ? "Active" : "In Active",
                    "description" => $product->description,
                    "category" => $product->category->name ?? null,
                    "image" => !empty($product->image)
                        ? asset("storage/uploads/product/" . $product->image)
                        : "",
                    "varient" => $product->getdetail->map(function ($detail) use ($discountPercentage,$request,$product) {
                         $SellerOrderPrice = SellerOrderPrice::where('saller_id',$request->seller_id)->where('verient_id',$detail->varient->id)->where('product_id',$product->id)->latest()->first();
                        $price = !empty($SellerOrderPrice)?$SellerOrderPrice->price:$detail->retailer_price;
                        $mrp = $detail->mrp;
                        if ($mrp > 0) {
                            $discountPercentage = round((($price) / $mrp) * 100, 2);
                        }

                        $inclusivePrice = !empty($SellerOrderPrice)?$SellerOrderPrice->price:$detail->retailer_price;
                        $gstRate = $detail->gst;

                        if ($gstRate > 0) {
                            $basePrice = round($inclusivePrice / (1 + ($gstRate / 100)), 2);
                            $gstAmount = round($inclusivePrice - $basePrice, 2);
                            $cgst = $sgst = round($gstRate / 2, 2);
                        } else {
                            $basePrice = $inclusivePrice;
                            $cgst = $sgst = 0;
                        } 
                        return [
                            "id" => $detail->varient->id,
                            "price" => $price,
                            "mrp" => $mrp,
                            'gst' => $gstRate,
                            'cgst' => $cgst,
                            'sgst' => $sgst,
                            'exculsive_amount' => $basePrice,
                            "discount_percentage" => $discountPercentage,
                            "name" => $detail->varient
                                ? $detail->varient->name .
                                " " .
                                ($detail->varient->unit->name ?? "")
                                : null,
                        ];
                    }),
                ];
            });

            if ($data->isNotEmpty()) {
                ApiLogService::success(
                    sprintf(Message::PRODUCT_LIST, "PRODUCT"),
                    $data
                );
                return ApiResponseService::success(
                    sprintf(Message::PRODUCT_LIST, "PRODUCT"),
                    $data
                );
            } else {
                ApiLogService::success(
                    sprintf(Message::PTODUCT_LIST_NOT_FOUND, "PRODUCT"),
                    []
                );
                return ApiResponseService::success(
                    sprintf(Message::PTODUCT_LIST_NOT_FOUND, "PRODUCT"),
                    []
                );
            }
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(
                Message::SERVER_ERROR_MESSAGE,
                $e->getMessage(),
                500
            );
        }
    }

    public function addCart(Request $request)
    {
        try {
            ApiLogService::info("cart request received", $request->getContent());
            $finalAmt = 0;
            $finalAmtNotGst = 0;
            $data = (array) $this->jsonParse->parse($request->getContent());
            if (isset($data["seller_id"]) && !isset($data["cart_id"])) {
                $validator = Validator::make($data, [
                    "seller_id" => "required|integer|min:1|exists:tbl_sellers,id",
                    "staff_id" => "required|integer|min:1|exists:tbl_users,id",
                ]);

                if ($validator->fails()) {
                    ApiLogService::warning(Message::VALIDATION_MESSAGE, $validator->errors()->all());
                    return ApiResponseService::validation("Validation failed", $validator->errors()->all());
                }

                $cart = new Cart();
                $cart->seller_id = $data["seller_id"];
                $cart->staff_id = $data["staff_id"];
                $cart->save();

                return ApiResponseService::success("Cart created successfully", [
                    "cart_id" => $cart->id,
                    "cart_items" => [],
                ]);
            }
            if (isset($data["cart_id"])) {
                $validator = Validator::make($data, [
                    "cart_id" => "required|integer|exists:tbl_cart,id",
                    "items" => "required|array|min:1",
                    "items.*.product" => "required|array|min:1",
                    "items.*.product.*.id" => "required|integer",
                    "items.*.product.*.variants" => "required|array|min:1",
                    "items.*.product.*.variants.*.variant_id" => "required|integer",
                    "items.*.product.*.variants.*.qty" => "required|integer|min:1",
                ]);
                if ($validator->fails()) {
                    ApiLogService::warning(Message::VALIDATION_MESSAGE, $validator->errors()->all());
                    return ApiResponseService::validation("Validation failed", $validator->errors()->all());
                }
                $baseCart = Cart::find($data["cart_id"]);
                if (!$baseCart) {
                    return ApiResponseService::error("Base cart not found", null, 404);
                }
                $sellerId = $baseCart->seller_id;
                $staffId = $baseCart->staff_id;
                $requestedItems = [];
                foreach ($data["items"] as $itemGroup) {
                    foreach ($itemGroup["product"] as $product) {
                        foreach ($product["variants"] as $variant) {
                            $productId = (int)$product["id"];
                            $variantId = (int)$variant["variant_id"];
                            $qty = (int)$variant["qty"];
                            $key = "{$productId}-{$variantId}";
                            $requestedItems[$key] = compact('productId', 'variantId', 'qty');
                        }
                    }
                }
                $dbItems = Cart::where("seller_id", $sellerId)
                    ->where("staff_id", $staffId)
                    ->where("status", 0)
                    ->get();

                $existingMap = [];
                foreach ($dbItems as $dbItem) {
                    $key = "{$dbItem->product_id}-{$dbItem->varient_id}";
                    $existingMap[$key] = $dbItem;
                }
                foreach ($existingMap as $key => $dbItem) {
                    if (!isset($requestedItems[$key])) {
                        $dbItem->status = 1;
                        $dbItem->save();
                    }
                }
                foreach ($requestedItems as $key => $item) {
                    $productId = $item["productId"];
                    $variantId = $item["variantId"];
                    $qty = $item["qty"];
                    $SellerOrderPrice = SellerOrderPrice::where('saller_id',$sellerId)->where('verient_id',$variantId)->where('product_id',$productId)->latest()->first();

                    $pd = ProductDetail::where("product_id", $productId)
                        ->where("varient_id", $variantId)
                        ->first();
                    if (!$pd) continue;

                    $basePrice =  !empty($SellerOrderPrice)?(int)$SellerOrderPrice->price:$pd->retailer_price;
                    $totalPrice = $qty * $basePrice;

                    if (isset($existingMap[$key])) {
                        $existing = $existingMap[$key];
                        $existing->qty = $qty;
                        $existing->price = $totalPrice;
                        $existing->per_price = $basePrice;
                        $existing->status = 0;
                        $existing->save();
                    } else {
                        $new = new Cart();
                        $new->product_id = $productId;
                        $new->varient_id = $variantId;
                        $new->qty = $qty;
                        $new->price = $totalPrice;
                        $new->per_price = $basePrice;
                        $new->seller_id = $sellerId;
                        $new->staff_id = $staffId;
                        $new->status = 0;
                        $new->save();
                    }

                    $finalAmt +=$totalPrice;
                }
                $query = Cart::with([
                    "product:id,name",
                    "variant:id,name,unit_id",
                    "productDetail:product_id,retailer_price,gst,varient_id",
                    "staff:id,name",
                    "seller:id,name,shop_name,latitude,longitude,profile_pic,address",
                    "variant.unit",
                ])->where("seller_id", $sellerId)
                    ->where("staff_id", $staffId);

                $dbItemscount = Cart::where("seller_id", $sellerId)
                    ->where("staff_id", $staffId)
                    ->count();

                if (count($requestedItems) != $dbItemscount) {
                    $query->where('status', 0);
                }

                $cartItem = $query->get()->map(function ($d) use (&$finalAmtNotGst,$sellerId) {
                    $SellerOrderPrice = SellerOrderPrice::where('saller_id',$sellerId)->where('verient_id',$d->varient_id)->where('product_id',$d->product_id)->latest()->first();
                    $finalAmtNotGst += !empty($SellerOrderPrice)?(int)$SellerOrderPrice->price:$d->productDetail->retailer_price ?? 0;

                    return [
                        "cart_id" => $d->id,
                        "product_id" => $d->product_id,
                        "product_name" => optional($d->product)->name ?? '',
                        "variant_id" => $d->varient_id,
                        "variant_name" => optional($d->variant)->name && optional($d->variant->unit)->name
                            ? $d->variant->name . ' ' . $d->variant->unit->name
                            : '',
                        "qty" => $d->qty,
                        "mrp" =>  optional($d->productDetail)->retailer_price ?? '',
                        "gst" =>  optional($d->productDetail)->gst ?? '',
                        "staff_id" => $d->staff_id,
                        "staff_name" => optional($d->staff)->name ?? '',
                        "seller_name" => optional($d->seller)->name ?? '',
                        "seller_shop_name" => optional($d->seller)->shop_name ?? '',
                        "seller_address" => $d->seller->address ?? '',
                        "seller_profile_pic" => $d->seller && $d->seller->profile_pic
                            ? asset("storage/uploads/profile/" . $d->seller->profile_pic)
                            : '',
                        "perQtyPice" => $d->price,
                        "payment_mode" => 1,
                        "payment_mode_name" => "cash",
                        "weight" => $this->convertWeightToAll([
                            [
                                "variant_id" => $d->varient_id,
                                "qty" => $d->qty,
                            ]
                        ]),
                    ];
                });

                return ApiResponseService::success("Cart updated successfully", [
                    "totalPriceIncludeGst" => $finalAmt,
                    "cart" => $cartItem,
                ]);
            }

            return ApiResponseService::error("Invalid input data", null, 400);
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }


    public function cartList(Request $request)
    {
        try {
            ApiLogService::info("product request received", $request->all());
            $validator = Validator::make($request->all(), [
                "seller_id" => "required|integer|min:1|exists:tbl_sellers,id",
                "staff_id" => "required|integer|min:1|exists:tbl_users,id",
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validation("Validation failed", $validator->errors()->all());
            }
            $query = Cart::with([
                "product:id,name",
                "variant:id,name,unit_id",
                "productDetail:product_id,retailer_price,gst,varient_id",
                "staff:id,name",
                "seller:id,name,shop_name,latitude,longitude,profile_pic,address",
                "variant.unit",
            ])->where([
                ["seller_id", $request->seller_id],
                ["staff_id", $request->staff_id],
                ["status", 0],

            ]);
            $data = $query->get()->map(function ($d) {
                $SellerOrderPrice = SellerOrderPrice::where('saller_id',$request->seller_id)->where('verient_id',$d->varient_id)->where('product_id',$d->product_id)->latest()->first();
                $finalAmtNotGst += !empty($SellerOrderPrice)?(int)$SellerOrderPrice->price:$d->productDetail->retailer_price ?? 0;
                return [
                    "cart_id" => $d->id,
                    "product_id" => $d->product_id,
                    "product_name" => optional($d->product)->name ?? '',
                    "variant_id" => $d->varient_id,
                    "variant_name" => optional($d->variant)->name
                        && optional($d->variant->unit)->name
                        ? $d->variant->name . ' ' . $d->variant->unit->name
                        : '',
                    "qty" => $d->qty,
                    "mrp" =>  optional($d->productDetail)->retailer_price ?? '',
                    "gst" =>  optional($d->productDetail)->gst ?? '',
                    "staff_id" => $d->staff_id,
                    "staff_name" => optional($d->staff)->name ?? '',
                    "seller_name" => optional($d->seller)->name ?? '',
                    "seller_shop_name" => optional($d->seller)->shop_name ?? '',
                    "seller_address" => $d->seller->address ?? '',
                    "seller_profile_pic" => $d->seller && $d->seller->profile_pic
                        ? asset("storage/uploads/profile/" . $d->seller->profile_pic)
                        : '',
                    "perQtyPice" =>  !empty($SellerOrderPrice)?(int)$SellerOrderPrice->price:$d->price,
                    "payment_mode" => 1,
                    "payment_mode_name" => "cash",
                    "weight" => $this->convertWeightToAll([[
                        "variant_id" => $d->varient_id,
                        "qty" => $d->qty,
                    ]]),
                ];
            });
            if ($data->isNotEmpty()) {
                ApiLogService::success(
                    sprintf(Message::CART_SUCCESS, "Cart"),
                    $data
                );
                return ApiResponseService::success(
                    sprintf(Message::PRODUCT_LIST, "Cart"),
                    $data
                );
            } else {
                ApiLogService::success(
                    sprintf(Message::PTODUCT_LIST_NOT_FOUND, "Cart"),
                    []
                );
                return ApiResponseService::success(
                    sprintf(Message::PTODUCT_LIST_NOT_FOUND, "Cart"),
                    []
                );
            }
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(
                Message::SERVER_ERROR_MESSAGE,
                $e->getMessage(),
                500
            );
        }
    }



    private function convertWeightToAll(array $variantData = [])
    {
        $totalGrams = 0;
        foreach ($variantData as $item) {
            $variantId = $item["variant_id"] ?? null;
            $qty = $item["qty"] ?? 0;
            if (!$variantId || !$qty) {
                continue;
            }
            $variant = Varient::with("unit")->find($variantId);
            if ($variant && $variant->unit) {
                $unit = strtolower(trim($variant->unit->name));
                $weight = floatval($variant->name);
                $gramsPerPiece = 1000;
                if (in_array($unit, ["kg", "kgs", "kilogram", "kilograms"])) {
                    $gramsPerPiece = $weight * 1000;
                } elseif (in_array($unit, ["g", "gms", "gram", "grams"])) {
                    $gramsPerPiece = $weight;
                }
                $totalGrams += $qty * $gramsPerPiece;
            }
        }
        return [
            "pcs" => array_sum(array_column($variantData, "qty")),
            "grams" => round($totalGrams, 2),
            "kilograms" => round($totalGrams / 1000, 2),
        ];
    }

    
   public function addProductSpecialPrice(Request $request)
   {
    $validator = Validator::make($request->all(), [
        'sallerId'        => 'required|integer|min:1|exists:tbl_sellers,id',
        'staffId'         => 'required|integer|min:1|exists:tbl_users,id',
        'productId'       => 'required|integer|exists:tbl_product,id',
        'verientAndPrice' => 'required|array',
        'verientAndPrice.*' => 'integer|min:1' 
    ], [
        'sallerId.required'        => 'The sallerId field is required.',
        'staffId.required'         => 'The staffId field is required.',
        'productId.required'       => 'The productId field is required.',
        'verientAndPrice.required' => 'The verientAndPrice field is required.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            "status" => false,
            "msg"    => $validator->errors()->all()
        ], 422);
    }

    try {
        $data = collect($request->verientAndPrice)->map(function ($price, $verientId) use ($request) {
            return [
                'saller_id'  => $request->sallerId,
                'staff_id'   => $request->staffId,
                'product_id' => $request->productId,
                'verient_id' => $verientId,
                'price'      => $price,
            ];
        })->values()->toArray();

        SellerOrderPrice::insert($data);

        return response()->json([
            "status" => true,
            "msg"    => "Special prices added successfully."
        ], 200);

    } catch (\Exception $e) {
        ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
        return ApiResponseService::error(
            Message::SERVER_ERROR_MESSAGE,
            $e->getMessage(),
            500
        );
    }
}




}
