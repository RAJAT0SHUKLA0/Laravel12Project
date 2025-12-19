 @extends('admin.layout.layout')
 @section('content')
     <style>
         .tbl_td_padding {
             padding: .20rem 1rem !important;
         }

         .timeline-item {
             padding: 0px !important;
         }
     </style>
     <div class="page-content">
         <div class="container-fluid">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="card">
                         <div class="card-header">
                             <div class="d-flex align-items-center">
                                 <h5 class="card-title flex-grow-1 mb-0">Order No:-
                                     {{ isset($orderList->order_id) ? $orderList->order_id : '' }}</h5>



                                 <div class="flex-shrink-0">
                                     <!--<a href="apps-invoices-details.html" class="btn btn-success btn-sm"><i class="ri-download-2-fill align-middle me-1"></i> Invoice</a>-->
                                     <a href="@encryptedRoute('generate-order-detail-invoice', $orderList->id)" class="btn btn-success btn-sm"><i
                                             class="ri-download-2-fill align-middle me-1"></i> Invoice</a>
                                 </div>
                             </div>
                         </div>
                         <div class="card-body">
                             <div class="table-responsive table-card">
                                 <table class="table table-nowrap align-middle table-borderless mb-0">
                                     <thead class="table-light text-muted">
                                         <tr>
                                             <th scope="col">Seller Name</th>
                                             <th scope="col">Shop Name</th>
                                             <th scope="col">Staff Name</th>
                                             <th scope="col">Total Price</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         <tr>

                                             <td>{{ isset($orderList->seller->name) ? $orderList->seller->name : '' }}</td>
                                             <td>{{ isset($orderList->seller->shop_name) ? $orderList->seller->shop_name : '' }}
                                             </td>
                                             <td>{{ isset($orderList->staff->name) ? $orderList->staff->name : '' }}</td>
                                             <td>
                                                 {{ config('constants.INDIAN_RUPEE_SYMBOL') . ' ' . $orderList->total_price }}
                                             </td>
                                         </tr>
                                     </tbody>

                                     <!--new added  4 aug -->
                                     <!--@dump($orderList);-->

                                     <thead class="table-light text-muted">
                                         <tr>
                                             <th scope="col">Order Date</th>
                                             @if ($orderList->status == 4)
                                                 <th scope="col">Cancel Date</th>
                                             @else
                                                 <th scope="col">Delivery Date</th>
                                                 <th scope="col">Pickup Date</th>
                                             @endif
                                             <th scope="col">Order Status</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         <tr>
                                             <td>{{ isset($orderList->date) ? $orderList->date : 'N/A' }}</td>
                                             @if ($orderList->status == 4)
                                                 <td>
                                                     {{ !empty($orderList->cancel_date) && $orderList->cancel_date != '0000-00-00'
                                                         ? \Carbon\Carbon::parse($orderList->cancel_date)->format('d-m-Y')
                                                         : 'N/A' }}
                                                 </td>
                                             @else
                                                 <td>
                                                     {{ !empty($orderList->delivery_date) && $orderList->delivery_date != '0000-00-00'
                                                         ? \Carbon\Carbon::parse($orderList->delivery_date)->format('d-m-Y')
                                                         : 'N/A' }}
                                                 </td>

                                                 <td>
                                                     {{ !empty($orderList->pickup_date) && $orderList->pickup_date != '0000-00-00'
                                                         ? \Carbon\Carbon::parse($orderList->pickup_date)->format('d-m-Y')
                                                         : 'N/A' }}
                                                 </td>
                                             @endif

                                             <td>
                                                 @if (isset($orderList->status))
                                                     @switch($orderList->status)
                                                         @case(0)
                                                             <span class="badge bg-warning text-dark">Order Pending</span>
                                                         @break

                                                         @case(1)
                                                             <span class="badge bg-primary">Order Approved</span>
                                                         @break

                                                         @case(2)
                                                             <span class="badge bg-info text-dark">Order Pickup</span>
                                                         @break

                                                         @case(3)
                                                             <span class="badge bg-success">Order Delivered</span>
                                                         @break

                                                         @case(4)
                                                             <span class="badge bg-danger">Order Cancelled</span>
                                                         @break

                                                         @case(5)
                                                             <span class="badge bg-secondary">Order Returned</span>
                                                         @break

                                                         @case(6)
                                                             <span class="badge bg-dark">Order Assigned</span>
                                                         @break

                                                         @default
                                                             <span class="badge bg-light text-dark">Unknown Status</span>
                                                     @endswitch
                                                 @else
                                                     <span class="badge bg-danger">Order status not found.</span>
                                                 @endif
                                             </td>
                                         </tr>
                                     </tbody>
                                     <!--end updates -->
                                 </table>

                             </div>
                         </div>

                         <div class="card-body">
                             <div class="table-responsive table-card">
                                 <table class="table table-nowrap align-middle table-borderless mb-0">
                                     <thead class="table-light text-muted">
                                         <tr>
                                             <th scope="col">Product Details</th>
                                             <th scope="col">Item Price</th>
                                             <th scope="col">Gst</th>
                                             <th scope="col">Quantity</th>
                                             <th scope="col" class="text-end">Total Amount</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         @php
                                             $subTotal = 0;
                                             $gstTax = 0;
                                             $cgst = 0;
                                             $sgst = 0;
                                         @endphp



                                         @if (sizeof($orderList->orderDetails))
                                             @foreach ($orderList->orderDetails as $orderdetail)
                                                 @php
                                                     $retailerPrice = $orderdetail->per_price ?? 0;
                                                     $subTotal += $retailerPrice * $orderdetail->qty;

                                                     $gstTax = $subTotal * ($orderdetail->productDetail->gst / 100);
                                                     $cgst = $subTotal * ($orderdetail->productDetail->gst / 100 / 2);
                                                     $sgst = $subTotal * ($orderdetail->productDetail->gst / 100 / 2);

                                                 @endphp

                                                 <tr>
                                                     <td>
                                                         <div class="d-flex">
                                                             <div class="flex-shrink-0 bg-light rounded p-1">
                                                                 <img src="{{ asset('storage/uploads/product/' . $orderdetail->product->image) }}"
                                                                     alt="" class=""
                                                                     style="height:50px;width:auto;">
                                                             </div>
                                                             <div class="flex-grow-1 ms-3">
                                                                 <h5 class="fs-15"><a
                                                                         href="apps-ecommerce-product-details.html"
                                                                         class="link-primary">{{ isset($orderdetail->product->name) ? $orderdetail->product->name : '' }}</a>
                                                                 </h5>
                                                                 <p class="text-muted mb-0"><span
                                                                         class="fw-medium">{{ isset($orderdetail->product->description) ? $orderdetail->product->description : '' }}</span>
                                                                 </p>

                                                             </div>
                                                         </div>
                                                     </td>
                                                     <td>{{ config('constants.INDIAN_RUPEE_SYMBOL') . ' ' . $retailerPrice }}
                                                     </td>
                                                     <td> {{ isset($orderdetail->productDetail->gst) ? $orderdetail->productDetail->gst . '%' : '' }}
                                                     </td>
                                                     <td>{{ isset($orderdetail->qty) ? $orderdetail->qty : '' }}</td>
                                                     <td class="fw-medium text-end">
                                                         {{ config('constants.INDIAN_RUPEE_SYMBOL') . ' ' . $retailerPrice * $orderdetail->qty }}
                                                     </td>
                                                 </tr>
                                             @endforeach
                                         @endif

                                         <tr class="border-top border-top-dashed">
                                             <td colspan="3"></td>
                                             <td colspan="2" class="fw-medium p-0">
                                                 <table class="table table-borderless mb-0">
                                                     <tbody>
                                                         <tr>
                                                             <td>Sub Total :</td>
                                                             <td class="text-end">
                                                                 {{ config('constants.INDIAN_RUPEE_SYMBOL') . ' ' . $subTotal }}
                                                             </td>
                                                         </tr>
                                                         <!--<tr>-->
                                                         <!--    <td>Discount <span class="text-muted">(VELZON15)</span> : :</td>-->
                                                         <!--    <td class="text-end">-$53.99</td>-->
                                                         <!--</tr>-->
                                                         <!--<tr>-->
                                                         <!--    <td>Shipping Charge :</td>-->
                                                         <!--    <td class="text-end">$65.00</td>-->
                                                         <!--</tr>-->
                                                         <tr>
                                                             <td class="tbl_td_padding">Cgst Included(2.5%) :</td>
                                                             <td class="text-end tbl_td_padding">
                                                                 {{ config('constants.INDIAN_RUPEE_SYMBOL') }}
                                                                 {{ isset($cgst) ? $cgst : '' }}</td>
                                                         </tr>

                                                         <tr>
                                                             <td class="tbl_td_padding">Sgst Included(2.5%) :</td>
                                                             <td class="text-end tbl_td_padding">
                                                                 {{ config('constants.INDIAN_RUPEE_SYMBOL') }}
                                                                 {{ isset($sgst) ? $sgst : '' }}</td>
                                                         </tr>

                                                         <tr>
                                                             <td class="tbl_td_padding">Gst Included(5%) :</td>
                                                             <td class="text-end tbl_td_padding">
                                                                 {{ config('constants.INDIAN_RUPEE_SYMBOL') }}
                                                                 {{ isset($gstTax) ? $gstTax : '' }}</td>
                                                         </tr>

                                                         <tr>
                                                             <td class="tbl_td_padding">Total Discount :</td>
                                                             <td class="text-end tbl_td_padding">-
                                                                 {{ config('constants.INDIAN_RUPEE_SYMBOL') }}
                                                                 {{ isset($orderList->discount) ? $orderList->discount : '' }}
                                                             </td>
                                                         </tr>

                                                         @php
                                                             $finalTotal =
                                                                 $orderList->discount > 0
                                                                     ? $orderList->total_price - $orderList->discount
                                                                     : $orderList->total_price;
                                                             $rounded = round($finalTotal);
                                                         @endphp

                                                         @if (fmod($finalTotal, 1) != 0)
                                                             <tr>
                                                                 <td class="tbl_td_padding">Round Off</td>
                                                                 <td class="text-end tbl_td_padding">
                                                                     {{ config('constants.INDIAN_RUPEE_SYMBOL') }}
                                                                     {{ $rounded }}
                                                                 </td>
                                                             </tr>
                                                         @endif

                                                         <tr class="border-top border-top-dashed">
                                                             <th scope="row">Total
                                                                 ({{ config('constants.INDIAN_RUPEE_SYMBOL') }}) :</th>
                                                             <th class="text-end">
                                                                 {{ config('constants.INDIAN_RUPEE_SYMBOL') }}
                                                                 {{ $rounded }}
                                                             </th>
                                                         </tr>



                                                     </tbody>
                                                 </table>
                                             </td>
                                         </tr>
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                     <!--end card-->
                     @php
                         $statusLabels = [
                             0 => 'Pending',
                             6 => 'Assign',
                             2 => 'Pickup',
                             1 => 'To Delivered',
                             3 => 'Delivered',
                             4 => 'Cancel',
                             5 => 'Return',
                         ];

                         $statusClasses = [
                             'bg-warning-subtle text-warning', // Pending
                             'bg-secondary-subtle text-secondary', // To Delivered
                             'bg-info-subtle text-info', // Pickup
                             'bg-success-subtle text-success', // Delivered
                             'bg-danger-subtle text-danger', // Cancel
                             'bg-danger-subtle text-danger', // Return
                             'bg-primary-subtle text-primary', // Assign
                         ];
                         $statusLabels2 = [
                             0 => 'Pending',
                             6 => 'Assign',
                             2 => 'Pickup',
                             1 => 'To Delivered',
                             3 => 'Delivered',
                             4 => 'Cancel',
                             5 => 'Return',
                         ];
                         $currentStatus = $orderList->status ?? '0'; // Assuming you get this from DB
                     @endphp
                     <div class="card">
                         <div class="card-header">
                             <div class="d-sm-flex align-items-center">
                                 <h5 class="card-title flex-grow-1 mb-0">Order Status</h5>
                                 <!--<a href="@encryptedRoute('cancelOrder', $orderList->id)" class="btn btn-sm btn-danger"> Cancel Order </a>-->
                                 <div class="flex-shrink-0 mt-2 mt-sm-0">
                                     @php
                                         $status = $statusLabels2[$currentStatus];
                                         $statusKeys = array_keys($statusLabels);
                                         $currentIndex = array_search($currentStatus, $statusKeys);
                                     @endphp
                                     @if ($status == 'Pending' || $status == 'To Delivered')
                                         <!--<a href="javascript:void(0);"-->
                                         <!--    class="btn btn-soft-warning btn-sm mt-2 sweet-confirm"-->
                                         <!--    data-title="Change Status?" data-text="Do you want to move to Pickup?"-->
                                         <!--    data-confirm="Yes, change it!" data-success="Status updated successfully!"-->
                                         <!--    data-href="@encryptedRoute('orderStatus', $orderList->id, 2)" data-bs-toggle="popover"-->
                                         <!--    data-bs-trigger="hover focus" data-bs-content="Pickup Order">-->
                                         <!--    <i class="mdi mdi-archive-remove-outline align-middle me-1"></i> Pickup-->
                                         <!--</a>-->

                                         <a href="javascript:void(0);" class="btn btn-danger btn-sm mt-2 sweet-confirm"
                                             data-title="Change Status?" data-text="Do you want to cancel the order?"
                                             data-confirm="Yes, cancel it!" data-success="Order cancelled!"
                                             data-href="@encryptedRoute('orderStatus', $orderList->id, 4)" data-bs-toggle="popover"
                                             data-bs-trigger="hover focus" data-bs-content="Cancel Order">
                                             <i class="mdi mdi-close-circle-outline align-middle me-1"></i> Cancel
                                         </a>
                                     @elseif($status == 'Pickup')
                                         <!--<a href="javascript:void(0);"-->
                                         <!--    class="btn btn-soft-success btn-sm mt-2 sweet-confirm"-->
                                         <!--    data-title="Change Status?" data-text="Deliver this order now?"-->
                                         <!--    data-confirm="Yes, deliver!" data-success="Delivered successfully!"-->
                                         <!--    data-href="@encryptedRoute('orderStatus', $orderList->id, 1)" data-bs-toggle="popover"-->
                                         <!--    data-bs-trigger="hover focus" data-bs-content="Deliver Order">-->
                                         <!--    <i class="mdi mdi-truck-delivery-outline align-middle me-1"></i> Deliver-->
                                         <!--</a>-->
                                     @endif

                                 </div>
                             </div>
                         </div>

                         <div class="card-body">
                             <div class="profile-timeline">
                                 <div class="accordion accordion-flush" id="accordionFlushExample">

                                     @foreach ($statusLabels as $index => $status)
                                         @php
                                             $statusIndex = array_search($index, $statusKeys);
                                             $isActive = $statusIndex <= $currentIndex;
                                             $isExpanded = $status === $currentStatus;

                                             // Icons
                                             $icons = [
                                                 'Pending' => 'ri-time-line',
                                                 'Delivered' => 'mdi mdi-package-variant',
                                                 'Pickup' => 'mdi mdi-truck-delivery-outline',
                                                 'Cancel' => 'mdi mdi-close-circle-outline',
                                                 'Assign' => 'mdi mdi-account-arrow-right',
                                             ];

                                             // Dates
                                             switch ($status) {
                                                 case 'Pending':
                                                     $statusDate =
                                                         $orderList->date == '0000-00-00' || $orderList->date == null
                                                             ? null
                                                             : $orderList->date;
                                                     break;
                                                 case 'Assign':
                                                     $statusDate =
                                                         $orderList->assign_date == '0000-00-00' ||
                                                         $orderList->assign_date == null
                                                             ? null
                                                             : $orderList->assign_date;
                                                     break;
                                                 case 'To Delivered':
                                                     $statusDate =
                                                         $orderList->pickup_date == '0000-00-00' ||
                                                         $orderList->pickup_date == null
                                                             ? null
                                                             : $orderList->pickup_date;
                                                     break;
                                                 case 'Pickup':
                                                     $statusDate =
                                                         $orderList->pickup_date == '0000-00-00' ||
                                                         $orderList->pickup_date == null
                                                             ? null
                                                             : $orderList->pickup_date;
                                                     break;
                                                 case 'Delivered':
                                                     $statusDate =
                                                         $orderList->delivery_date == '0000-00-00' ||
                                                         $orderList->delivery_date == null
                                                             ? null
                                                             : $orderList->delivery_date;
                                                     break;
                                                 case 'Cancel':
                                                     $statusDate =
                                                         $orderList->cancel_date == '0000-00-00' ||
                                                         $orderList->cancel_date == null
                                                             ? null
                                                             : $orderList->cancel_date;
                                                     break;
                                                 default:
                                                     $statusDate = null;
                                             }

                                             $formattedDate = $statusDate
                                                 ? \Carbon\Carbon::parse($statusDate)->format('D, d M Y')
                                                 : 'N/A';
                                             $formattedDateTime = $statusDate
                                                 ? \Carbon\Carbon::parse($statusDate)->format('D, d M Y - h:iA')
                                                 : 'N/A';
                                         @endphp

                                         {{-- Line connector before (except first item) --}}
                                         @if ($index > 0)
                                             <div class="timeline-line {{ $isActive ? 'bg-success' : 'bg-light' }}"></div>
                                         @endif

                                         <div
                                             class="timeline-item position-relative ps-4 mb-4 {{ $isActive ? 'active' : '' }}">
                                             <div class="accordion-item border-0">
                                                 <div class="accordion-header" id="heading{{ $index }}">
                                                     <a class="accordion-button p-2 shadow-none {{ $isExpanded ? '' : 'collapsed' }}"
                                                         data-bs-toggle="collapse" href="#collapse{{ $index }}"
                                                         aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
                                                         aria-controls="collapse{{ $index }}">
                                                         <div class="d-flex align-items-center">
                                                             <div class="flex-shrink-0 avatar-xs">
                                                                 <div
                                                                     class="avatar-title rounded-circle material-shadow {{ $isActive ? 'bg-success text-white' : 'bg-light text-muted' }}">
                                                                     <i
                                                                         class="{{ $icons[$status] ?? 'ri-information-line' }}"></i>
                                                                 </div>
                                                             </div>
                                                             <div class="flex-grow-1 ms-3">
                                                                 <h6 class="fs-15 mb-0 fw-semibold">
                                                                     {{ $status }} -
                                                                     <span class="fw-normal">{{ $formattedDate }} </span>
                                                                 </h6>
                                                             </div>
                                                         </div>
                                                     </a>
                                                 </div>
                                                 <div id="collapse{{ $index }}"
                                                     class="accordion-collapse collapse {{ $isExpanded ? 'show' : '' }}"
                                                     aria-labelledby="heading{{ $index }}"
                                                     data-bs-parent="#accordionFlushExample">
                                                     <div class="accordion-body ms-2 ps-5 pt-0">
                                                         <h6 class="mb-1">Status: {{ $status }}</h6>
                                                         <p class="text-muted mb-0">{{ $formattedDate }}</p>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     @endforeach

                                 </div>
                             </div>
                         </div>


                     </div>

                     <!--  <div class="card">-->
                     <!--    <div class="card-header">-->
                     <!--        <div class="d-sm-flex align-items-center">-->
                     <!--            <h5 class="card-title flex-grow-1 mb-0">Rider Details</h5>-->
                     <!--        </div>-->
                     <!--    </div>-->

                     <!--    <div class="card-body">-->
                             
                     <!--    </div>-->


                     <!--</div>-->
                     <!--end card-->
                 </div>

             </div>

         </div><!-- container-fluid -->
     </div>
 @endsection
