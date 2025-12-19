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
use Carbon\Carbon;
use App\Models\Transaction;
use Mpdf\Mpdf;
use App\Utils\Uploads;
use App\Models\OrderAssign;
use App\Models\User;
use App\Models\Seller;
use App\Helper\HelperFunction;
use App\Models\SellerOrderPrice;
use App\Models\OrderAssignShopLocationDetail;
use Illuminate\Support\Facades\File;
use App\Utils\Crypto;
use App\Services\FcmService;
use App\Notifications\Payloads\OrderPayloads;

class OrderController extends Controller
{
    private $jsonParse;
    private $mpdf;
    protected $fcm;
    public function __construct(JsonParserService $JsonParserService, Mpdf $mpdf, FcmService $fcm)
    {
        $this->jsonParse = $JsonParserService;
        $this->mpdf = $mpdf;
        $this->fcm = $fcm;
    }
    public function createOrder(Request $request)
    {

        try {
            $jsonInput = $request->getContent();
            $jsonParseData = $this->jsonParse->parse($jsonInput);
            $data = is_array($jsonParseData) ? $jsonParseData : (array)$jsonParseData;
            $validator = Validator::make($data, [
                "cart_id" => "required|array|min:1",
                "cart_id.*" => "required|integer|exists:tbl_cart,id",
                'delivery_date' => [
                    'date',
                    'after_or_equal:' . now()->format('Y-m-d'),
                    'before_or_equal:' . now()->addDays(3)->format('Y-m-d'),
                ],
                "discount" => "nullable|numeric|min:0",

            ]);
            if ($validator->fails()) {
                return ApiResponseService::validation("Validation failed", $validator->errors()->all());
            }
            $cartItems = Cart::whereIn("id", $data["cart_id"])->where('status', '!=', '1')->where('qty', '>', '0')->get();
            if ($cartItems->isEmpty()) {
                return ApiResponseService::error("No cart data found", null, 404);
            }
            $firstCart = $cartItems->first();
            $sellerId = $firstCart->seller_id;
            $staffId = $firstCart->staff_id;
            $totalAmount = $cartItems->sum("price");
            $discount = isset($data['discount']) ? $data['discount'] : 0;
            if ($discount > $totalAmount) {
                return ApiResponseService::validation("Validation failed", ["Discount cannot exceed total cart amount (₹{$totalAmount})"]);
            }
            $finalAmts = $totalAmount - $discount;
            $order = new Order();
            $order->order_id = "#" . HelperFunction::generateRandNumber();
            $order->seller_id = $sellerId;
            $order->staff_id = $staffId;
            $order->total_price = $finalAmts;
            $order->date = now();
            $order->discount = $discount;
            $order->save();
            foreach ($cartItems as $cart) {
                $orderDetails = new OrderDetail();
                $orderDetails->order_id = $order->id;
                $orderDetails->product_id = $cart->product_id;
                $orderDetails->varient_id = $cart->varient_id;
                $orderDetails->qty = $cart->qty;
                $orderDetails->seller_id = $cart->seller_id;
                $orderDetails->staff_id = $cart->staff_id;
                $orderDetails->price = $cart->price;
                $orderDetails->per_price = $cart->per_price;
                $orderDetails->save();
            }
            $Transaction = new Transaction();
            $Transaction->order_id = $order->id;
            $Transaction->transaction_no = "#" . HelperFunction::generateRandNumber();
            $Transaction->date = now();
            $Transaction->amount = $finalAmts;
            $Transaction->deduct_amount = $finalAmts;
            $Transaction->seller_id = $sellerId;
            $Transaction->staff_id = $staffId;
            $Transaction->payment_mode = 1;
            $Transaction->status = 0;
            $Transaction->save();

            $topCartItem = $cartItems->sortByDesc('qty')->first();

            $orderImage = null;
            if ($topCartItem && $topCartItem->product) {
                $orderImage = $topCartItem->product->image
                    ? asset('storage/uploads/product/' . $topCartItem->product->image)
                    : null;
            }

            Cart::whereIn("id", $data["cart_id"])->delete();

            if ($order) {

                $user = auth()->user();
                // ✅ Push notification to  requesting user
                if ($user && $user->device_id) {
                    $route = 'manage_orders';
                    $date   = $order->date instanceof \Carbon\Carbon ? $order->date->format('d M Y') : $order->date;
                    $amount = config('constants.INDIAN_RUPEE_SYMBOL') . number_format($finalAmts, 2);
                    $message = "Order placed successfully on {$date} " . "for {$amount}.";
                    $payloadUser = OrderPayloads::createOrder(
                        $user->device_id,
                        "Order placed successfully {$order->order_id}",
                        $message,
                        $orderImage,
                        $route
                    );

                    $this->fcm->send($payloadUser);
                }

                // ✅ Push notification to Admin (user_id = 47)
                $admin = User::find(47);
                if ($admin && $admin->device_id) {
                    $route = 'manage_orders';
                    $date   = $order->date instanceof \Carbon\Carbon
                        ? $order->date->format('d M Y')
                        : $order->date;
                    $amount = config('constants.INDIAN_RUPEE_SYMBOL') . number_format($finalAmts, 2);
                    $message = "{$user->name} placed a new order on {$date} "
                        . "for {$amount}. Order ID is {$order->order_id}.";
                    $payloadAdmin = OrderPayloads::createOrder(
                        $admin->device_id,
                        "New Order Received",
                        $message,
                        $orderImage,
                        $route
                    );

                    $this->fcm->send($payloadAdmin);
                }
            }

            return ApiResponseService::success("Order placed successfully", [
                "order_id" => $order->id,
                "order_no" => $order->order_no,
                "total" => $totalAmount,
            ]);
        } catch (\Exception $e) {
            ApiLogService::error("Order creation failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }


    public function OrderList(Request $request)
    {
        try {
            ApiLogService::info('Leave request received', $request->all());
            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|integer||exists:tbl_users,id',

            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
            $statusLabels = [
                0 => 'Pending',
                1 => 'To Deliver',
                2 => 'Pickup',
                3 => 'Delivered',
                4 => 'Cancel',
                5 => 'Return',
                6 => 'Assign'
            ];
            $transactionStatusLabels = [
                0 => 'Pending',
                1 => 'remaining',
                2 => 'complete',
            ];
            $orderList = Order::with([
                'orderDetails',
                'seller:id,name,shop_name,latitude,longitude,profile_pic,beat_id,address',
                'staff:id,name',
                'orderDetails.product:id,name,image',
                'orderDetails.variant:id,name,unit_id',
                'orderDetails.variant.unit:id,name',
                'orderDetails.productDetail:product_id,varient_id,retailer_price',
                'seller.area'
            ])->where('staff_id', $request->staff_id)->orderby('id', 'desc')

                ->get()
                ->map(function ($order) use ($statusLabels, $transactionStatusLabels) {
                    return [
                        'order_id' => $order->id ?? '',
                        'order_no' => $order->order_id ?? '',
                        'discount' => $order->discount ?? '',
                        'staff_name' => $order->staff->name ?? '',
                        'seller_name' => $order->seller->name ?? '',
                        'beat_name' => $order->seller->area->name ?? '',
                        'beat_id' => $order->seller->area->id ?? '',
                        'seller_shop_name' => $order->seller->shop_name ?? '',
                        "seller_profile_pic" => $order->seller && $order->seller->profile_pic ? asset("storage/uploads/profile/" . $order->seller->profile_pic) : '',
                        'seller_address' => $order->seller->address ?? '',
                        'total_price' => $order->total_price,
                        'status' => $statusLabels[$order->status] ?? 'Unknown',
                        'invoice_link' => route('downloadPdf', [Crypto::encryptId($order->id)]),
                        'transaction_status' => optional($order->transaction->first())->status ? ($transactionStatusLabels[$order->transaction->first()->status] ?? $transactionStatusLabels[0]) : $transactionStatusLabels[0],
                        'order_date' => Carbon::parse($order->date)->format('Y-m-d'),
                        'order_details' => $order->orderDetails->map(function ($detail)  use ($order) {
                            $staffId = $order->staff->id ?? '';
                            $variantId = $detail->variant->id ?? '';
                            $sellerId = $order->seller->id ?? '';
                            $SellerOrderPrice = 0;
                            if ($staffId && $variantId) {
                                $SellerOrderPrice = SellerOrderPrice::where('staff_id', $staffId)->where('saller_id', $sellerId)
                                    ->where('verient_id', $variantId)
                                    ->where('product_id', $detail->product->id)
                                    ->latest()
                                    ->first();
                            }
                            $productName = $detail->product->name ?? '';
                            $variant = $detail->variant;
                            $unit = $variant ? $variant->unit : null;
                            $variantName = ($variant->name ?? '') . ' ' . ($unit->name ?? '');

                            $qty = $detail->qty;
                            $perPrice = $detail->per_price;
                            $totalPrice = $qty * $perPrice;
                            return [
                                'product_name' => $productName,
                                'variant_name' => $variantName,
                                'qty' => $qty,
                                "product_image" => $detail->product && $detail->product->image ? asset("storage/uploads/product/" . $detail->product->image) : '',
                                'per_price' => config('constants.INDIAN_RUPEE_SYMBOL') . ' ' . $perPrice,
                                'total_price' => $totalPrice,
                            ];
                        }),
                    ];
                });
            return ApiResponseService::success("Order list found successfully", $orderList);
        } catch (\Exception $e) {
            ApiLogService::error("Order creation failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }



    public function orderAssignByRiderList(Request $request)
    {
        try {
            $riderLatLog = explode(',', trim($request->rider_location));
            $statusLabels = [
                0 => 'Pending',
                1 => 'To Deliver',
                2 => 'Pickup',
                3 => 'Delivered',
                4 => 'Cancel',
                5 => 'Return',
                6 => 'Assign'
            ];

            $transactionStatusLabels = [
                0 => 'Pending',
                1 => 'Completed',
                2 => 'Failed'
            ];

            $assignedOrders = OrderAssign::where('rider_id', auth()->id())
                ->pluck('order_id')
                ->flatMap(fn($ids) => array_map('trim', explode(',', $ids)))
                ->filter()
                ->unique()
                ->values();
            $orders = Order::with([
                'staff:id,name',
                'seller:id,name,shop_name,profile_pic,address,beat_id,mobile,latitude,longitude',
                'seller.area:id,name',
                'transaction:id,order_id,status',
                'orderDetails',
                'orderDetails.product:id,name,image',
                'orderDetails.variant:id,name'
            ])
                ->whereIn('id', $assignedOrders)
                ->get();
            $orderList = $orders->map(function ($order) use ($statusLabels, $transactionStatusLabels, $riderLatLog) {
                $exist = OrderAssignShopLocationDetail::where('staff_id', auth()->id())->latest()->first();
                if (!empty($exist)) {
                    $distance = HelperFunction::haversineDistance($exist->rider_lat, $exist->rider_lng, $order->seller->latitude, $order->seller->longitude);
                }
                if (empty($exist)) {
                    $distance = HelperFunction::haversineDistance($riderLatLog[0], $riderLatLog[1], $order->seller->latitude, $order->seller->longitude);
                }
                return [
                    'order_id' => $order->id,
                    'order_no' => $order->order_id ?? '',
                    'discount' => $order->discount,
                    'staff_name' => $order->staff->name ?? '',
                    'seller_name' => $order->seller->name ?? '',
                    'beat_name' => $order->seller->area->name ?? '',
                    'beat_id' => $order->seller->area->id ?? '',
                    'seller_shop_name' => $order->seller->shop_name ?? '',
                    'seller_profile_pic' => $order->seller && $order->seller->profile_pic ? asset("storage/uploads/profile/" . $order->seller->profile_pic) : '',
                    'seller_address' => $order->seller->address ?? '',
                    'latitude' => $order->seller->latitude ?? '',
                    'longitude' => $order->seller->longitude ?? '',
                    'mobile' => $order->seller->mobile ?? '',
                    'total_price' => $order->total_price,
                    'status' => $statusLabels[$order->status] ?? 'Unknown',
                    'transaction_status' => optional($order->transaction->first())->status
                        ? ($transactionStatusLabels[$order->transaction->first()->status] ?? $transactionStatusLabels[0])
                        : $transactionStatusLabels[0],
                    'order_date' => Carbon::parse($order->date)->format('Y-m-d'),
                    'distance' => fmod($distance, 1) === 0.0 ? intval($distance) . ' Km' : number_format($distance, 1) . ' Km',
                    'order_details' => $order->orderDetails->map(function ($detail) {
                        $productName = $detail->product->name ?? '';
                        $variantName = $detail->variant->name ?? '';
                        $qty = $detail->qty;
                        $perPrice = $detail->per_price;
                        $totalPrice = $qty * $perPrice;

                        return [
                            'product_name' => $productName,
                            'variant_name' => $variantName,
                            'qty' => $qty,
                            'product_image' => $detail->product && $detail->product->image
                                ? asset("storage/uploads/product/" . $detail->product->image)
                                : '',
                            'per_price' => $perPrice,
                            'total_price' => $totalPrice,
                        ];
                    }),
                ];
            })->sortBy('distance')->values();;
            return ApiResponseService::success("Order list found successfully", $orderList);
        } catch (\Exception $e) {
            ApiLogService::error("Order list fetch failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }

    public function getAssignOrderBeatWise(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'beat_id'  => 'required',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validation("Validation failed", $validator->errors()->all());
            }

            $statusLabels = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Pickup',
                3 => 'Delivered',
                4 => 'Cancel',
                5 => 'Return',
                6 => 'Assign'
            ];

            $transactionStatusLabels = [
                0 => 'Pending',
                1 => 'Completed',
                2 => 'Failed'
            ];

            $sellerId = Seller::whereIn('beat_id', $request->beat_id)->pluck('id')->toArray();
            $assignedOrderIds = OrderAssign::whereIn('beat_id', $request->beat_id)
                ->pluck('order_id')
                ->flatMap(fn($orderId) => array_map('trim', explode(',', $orderId)))
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();
            $orders = Order::with([
                'staff:id,name',
                'seller:id,name,shop_name,profile_pic,address,beat_id',
                'seller.area:id,name',
                'transaction:id,order_id,status',
                'orderDetails',
                'orderDetails.product:id,name,image',
                'orderDetails.variant:id,name'
            ])
                ->whereIn('seller_id', $sellerId)
                ->whereNotIn('id', $assignedOrderIds)
                ->get();
            $orderList = $orders->map(function ($order) use ($statusLabels, $transactionStatusLabels) {
                return [
                    'order_id' => $order->id,
                    'order_no' => $order->order_id ?? '',
                    'discount' => $order->discount,
                    'staff_name' => $order->staff->name ?? '',
                    'seller_name' => $order->seller->name ?? '',
                    'beat_name' => $order->seller->area->name ?? '',
                    'beat_id' => $order->seller->area->id ?? '',
                    'seller_shop_name' => $order->seller->shop_name ?? '',
                    'seller_profile_pic' => $order->seller && $order->seller->profile_pic ? asset("storage/uploads/profile/" . $order->seller->profile_pic) : '',
                    'seller_address' => $order->seller->address ?? '',
                    'total_price' => $order->total_price,
                    'status' => $statusLabels[$order->status] ?? 'Unknown',
                    'transaction_status' => optional($order->transaction->first())->status
                        ? ($transactionStatusLabels[$order->transaction->first()->status] ?? $transactionStatusLabels[0])
                        : $transactionStatusLabels[0],
                    'order_date' => Carbon::parse($order->date)->format('Y-m-d'),
                    'order_details' => $order->orderDetails->map(function ($detail) {
                        $productName = $detail->product->name ?? '';
                        $variantName = $detail->variant->name ?? '';
                        $qty = $detail->qty;
                        $perPrice = $detail->per_price;
                        $totalPrice = $qty * $perPrice;

                        return [
                            'product_name' => $productName,
                            'variant_name' => $variantName,
                            'qty' => $qty,
                            'product_image' => $detail->product && $detail->product->image
                                ? asset("storage/uploads/product/" . $detail->product->image)
                                : '',
                            'per_price' => $perPrice,
                            'total_price' => $totalPrice,
                        ];
                    }),
                ];
            });

            return ApiResponseService::success("Order list found successfully", $orderList);
        } catch (\Exception $e) {
            ApiLogService::error("Order list fetch failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }

    public function orderAssignSave(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rider_id'  => 'required',
                'beat_id'   => 'required',
                'order_id'  => 'required|array|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validation("Validation failed", $validator->errors()->all());
            }
            $allAssignedOrders = [];

            $assignedOrderIds = OrderAssign::where('rider_id', $request->rider_id)
                ->get()
                ->flatMap(fn($assign) => explode(',', $assign->order_id))
                ->map(fn($id) => (int) trim($id))
                ->filter()
                ->unique();

            if ($assignedOrderIds->isNotEmpty()) {
                $statuses = Order::whereIn('id', $assignedOrderIds)->pluck('status');
                $allPicked = $statuses->every(fn($status) => (int)$status === 1);
                if ($allPicked) {
                    return ApiResponseService::info('Rider has picked orders. Cannot assign new ones until delivery is completed.');
                }

                // Block if any status is not 3 (delivered)
                $hasUndelivered = $statuses->contains(fn($status) => (int)$status == 1);
                if ($hasUndelivered) {
                    return ApiResponseService::info('Rider has undelivered orders. Cannot assign new ones.');
                }
            }
            $requestedOrderIds = $request->order_id;
            $dataArray = array();
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
                    Order::whereIn('id', $orders)->update(['status' => 6, 'order_assign_date' => now() , 'order_rider_id' => $request->rider_id]);
                     $allAssignedOrders = array_merge($allAssignedOrders, $orders);
                }
            }

            // ✅ Send notification only if orders were assigned
                if (count($allAssignedOrders)) {
                    $rider = User::find($request->rider_id);
                    $admin = User::find(47);
                    $orderCount = count($allAssignedOrders);
            
                    $route = 'rider_orders';
            
                    // ️ Rider notification
                    if ($rider && $rider->device_id) {
                        $message = "Today {$orderCount} order have been assigned to you.";
                        $payloadRider = OrderPayloads::orderAssign(
                            $rider->device_id,
                            "New Orders Assigned",
                            $message,
                            $route
                        );
                        $this->fcm->send($payloadRider);
                    }
            
                    // Admin notification
                    if ($admin && $admin->device_id) {
                        $message = "{$orderCount} orders have been assigned to rider {$rider->name} today.";
                        $payloadAdmin = OrderPayloads::orderAssign(
                            $admin->device_id,
                            "Orders Assigned to Rider",
                            $message,
                            $route
                        );
                        $this->fcm->send($payloadAdmin);
                    }
                }


            return ApiResponseService::success("Order assigned successfully", []);
        } catch (\Exception $e) {
            ApiLogService::error("Order assignment failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }


    public function markAsPickup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validation("Validation failed", $validator->errors()->all());
            }
             Order::where('id', $request->order_id)->update([
                'status' => 2,
                'pickup_date' => now()->format('Y-m-d'),
            ]);
            $allAssigned = OrderAssign::where('rider_id', auth()->id())
                ->pluck('order_id')
                ->flatMap(fn($ids) => array_map('trim', explode(',', $ids)))
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();
            $remaining = Order::whereIn('id', $allAssigned)
                ->where('status', '!=', 2)
                ->count();
            $showButton = ($remaining === 0);
            
             // ✅ Send notification only if orders were assigned
                    $order =  Order::where('id', $request->order_id)->first();
                    $rider = User::find($order->staff_id);
                    $admin = User::find(47);
                    
                    $route = 'rider_orders';
            
                    // Admin notification
                    if ($admin && $admin->device_id) {
                        $message = "Rider {$rider->name} picked up order {$order->order_id} successfully.";
                        $payloadAdmin = OrderPayloads::orderPickup(
                            $admin->device_id,
                            "Rider picked up order successfully.",
                            $message,
                            $route
                        );
                        $this->fcm->send($payloadAdmin);
                    }
                
            return ApiResponseService::success(
                "Order status updated to Pickup successfully",
                ['show_button' => $showButton]
            );
        } catch (\Exception $e) {
            ApiLogService::error("Order pickup update failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }
    
    public function startDeliveryOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rider_location' => 'required|string'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validation("Validation failed", $validator->errors()->all());
            }
            $riderLatLog = explode(',', trim($request->rider_location));
            $riderLat = (float) ($riderLatLog[0] ?? 0);
            $riderLng = (float) ($riderLatLog[1] ?? 0);

            $assignedOrderIds = OrderAssign::where('rider_id', auth()->id())->where('status', '!=', '1')
                ->pluck('order_id')
                ->flatMap(fn($orderId) => array_map('trim', explode(',', $orderId)))
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            // Update orders' status = 1
            Order::whereIn('id', $assignedOrderIds)->update([
                'status' => 1,
                'delivery_date' => now()->format('Y-m-d'),
            ]);

            // Also update assign status = 1
            OrderAssign::where('rider_id', auth()->id())->update([
                'status' => 1
            ]);
            
            

            $nearestOrder = null;
            $minDistance = PHP_FLOAT_MAX;

            // Loop to find the nearest seller
            foreach ($assignedOrderIds as $orderId) {
                $order = Order::with('seller')->find($orderId);
                if (!$order || !$order->seller) {
                    continue;
                }

                $sellerLat = (float) ($order->seller->latitude ?? 0);
                $sellerLng = (float) ($order->seller->longitude ?? 0);

                $distance = HelperFunction::haversineDistance($riderLat, $riderLng, $sellerLat, $sellerLng);

                if ($distance > 0 && $distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestOrder = $order;
                }
            }

            if ($nearestOrder) {
                $formattedDistance = fmod($minDistance, 1) === 0.0
                    ? intval($minDistance)
                    : number_format($minDistance, 1);

                OrderAssignShopLocationDetail::create([
                    'order_id' => $nearestOrder->id,
                    'seller_id' => $nearestOrder->seller_id,
                    'staff_id' => auth()->id(),
                    'rider_lat' => $riderLat,
                    'rider_lng' => $riderLng,
                    'distance' => (string) $formattedDistance,
                ]);

                  // ✅ Send notification only if orders were assigned
                    $rider = User::find(auth()->id());
                    $admin = User::find(47);
                    
                    $route = 'rider_orders';
                    // Admin notification
                    if ($admin && $admin->device_id) {
                        $message = "Rider {$rider->name} is out for delivery.";
                        $payloadAdmin = OrderPayloads::orderOutForDelivery(
                            $admin->device_id,
                            "Rider {$rider->name} is out for delivery.",
                            $message,
                            $route
                        );
                        $this->fcm->send($payloadAdmin);
                    }

                return ApiResponseService::success("Nearest order assigned and location saved successfully.");
            } else {
                return ApiResponseService::error("No nearby orders found with valid distance.", [], 404);
            }
        } catch (\Exception $e) {
            ApiLogService::error("Order delivery start failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }

    public function deliverOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rider_location' => [
                    'required',
                    'regex:/^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/'
                ],
                'order_id' => 'required|integer|exists:tbl_order,id',
            ]);
            if ($validator->fails()) {
                return ApiResponseService::validation("Validation failed", $validator->errors()->all());
            }
            [$riderLat, $riderLng] = array_map('floatval', explode(',', trim($request->rider_location)));
            $order = Order::with('seller')->find($request->order_id);
            if (!$order) {
                return ApiResponseService::error("Order not found.", [], 404);
            }
            if (!$order->seller) {
                return ApiResponseService::error("Seller not found for this order.", [], 404);
            }
            $sellerLat = (float) $order->seller->latitude;
            $sellerLng = (float) $order->seller->longitude;
            $distance = HelperFunction::haversineDistance($riderLat, $riderLng, $sellerLat, $sellerLng);
            $formattedDistance = fmod($distance, 1) === 0.0
                ? intval($distance)
                : number_format($distance, 1);
            $order->status = 3;
            $order->save();
            OrderAssignShopLocationDetail::create([
                'seller_id' => $order->seller_id,
                'staff_id' => auth()->id(),
                'rider_lat' => $riderLat,
                'rider_lng' => $riderLng,
                'distance' => (string) $formattedDistance,
            ]);
            
            
             // ✅ Send notification only if orders were assigned
                    $rider = User::find(auth()->id());
                    $admin = User::find(47);
                    
                    $route = 'rider_orders';
                    // Admin notification
                    if ($admin && $admin->device_id) {
                        $message = "Rider {$rider->name} delivered order {$order->order_id} successfully.";
                        $payloadAdmin = OrderPayloads::orderdelivery(
                            $admin->device_id,
                            "Rider {$rider->name} delivered order successfully.",
                            $message,
                            $route
                        );
                        $this->fcm->send($payloadAdmin);
                    }
            
            return ApiResponseService::success("Order delivered successfully.");
        } catch (\Exception $e) {
            ApiLogService::error("Order delivery failed", $e);
            return ApiResponseService::error("Server error", $e->getMessage(), 500);
        }
    }


    public function downloadPdf($id = '')
    {
        try {
            $decryptedId = Crypto::decryptId($id);
            $hashName = sha1($decryptedId);
            $folderPath = public_path('storage/invoice');
            $fileName = $hashName . '.pdf';
            $filePath = "{$folderPath}/{$fileName}";
            $fileUrl = asset("storage/invoice/{$fileName}");


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
                'orientation' => 'P',
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while generating the invoice.',
            ], 500);
        }
    }
}
