@extends('admin.layout.layout')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="orderList">
                        <div class="card-header border-0">
                            <div class="row align-items-center gy-3">
                                <div class="col-sm">
                                    <h5 class="card-title mb-0">Order List</h5>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="d-flex gap-1 flex-wrap">
                                        <!--<button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Create Order</button>-->
                                        <!--<button type="button" class="btn btn-info"><i class="ri-file-download-line align-bottom me-1"></i> Import</button>-->
                                        <button class="btn btn-soft-danger" id="remove-actions" onclick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body border border-dashed border-end-0 border-start-0">
                            <form action="{{route('orderReport')}}" method='POST'>
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12 col-md-2">
                                         <label class="form-label">Enter Order Id</label>
                              <div class="input-group">
                                            <input type="text"  name= "order_id" class="form-control" placeholder="Select order Id" value="{{ request('order_id') }}">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-12 col-md-2">
                                        <label class="form-label">Choose Order Date</label>
                              <div class="input-group">
                                            <input type="text" name= "order_date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true" id="datepicker" placeholder="Select order date" value="{{ request('order_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label class="form-label">Choose Delivery Date</label>
                              <div class="input-group">
                                            <input type="text"  name= "delivery_date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true" id="datepicker" placeholder="Select delivery date" value="{{ request('delivery_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label class="form-label">Select Staff</label>
                              <div class="input-group">
                                            <select class="form-control" data-choices="" data-choices-search-false="" name="staff_id" id="idPayment" >
                                                <option value="">Select Staff</option>
                                                @if(sizeof($Staff))
                                                @foreach($Staff as $staffList)
                                                <option value="{{isset($staffList->id)?$staffList->id:''}}" {{ isset($staffList->id) && old('status', request('staff_id')) == $staffList->id ? 'selected' : '' }}>{{isset($staffList->name)?$staffList->name:''}}</option>
                                               @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                      <label class="form-label">Select Seller</label>
                                    <div class="input-group">
                                            <select class="form-control" data-choices="" data-choices-search-false="" name="seller_id" id="idPayment">
                                                <option value="">Select Seller</option>
                                                @if(sizeof($Seller))
                                                @foreach($Seller as $sellerList)
                                                <option value="{{isset($sellerList->id)?$sellerList->id:''}}" {{ isset($sellerList->id) && old('status', request('seller_id')) == $sellerList->id ? 'selected' : '' }}>{{isset($sellerList->name)?$sellerList->name:''}}</option>
                                               @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-12 col-md-2">
                                        <label class="form-label">Select Order Status</label>
                                    <div class="input-group">
                                            <select class="form-control" data-choices="" data-choices-search-false="" name="order_status" id="idStatus">
                                                <option value="">Status</option>
                                                <option value="0" {{ old('order_status', request('order_status')) == '0' ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ old('order_status', request('order_status')) == '1' ? 'selected' : '' }}>To Deliver</option>
                                                <option value="2" {{ old('order_status', request('order_status')) == '2' ? 'selected' : '' }}>Pickups</option>
                                                <option value="3" {{ old('order_status', request('order_status')) == '3' ? 'selected' : '' }}>Delivered</option>
                                                <option value="4" {{ old('order_status', request('order_status')) == '4' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="5" {{ old('order_status', request('order_status')) == '5' ? 'selected' : '' }}>Return</option>
                                                <option value="6" {{ old('order_status', request('order_status')) == '6' ? 'selected' : '' }}>Assigned</option>
                                            </select>
                                        </div>
                                    </div>
                                  <div class="col-12 col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <a href="{{ route('orderReport') }}" class="btn btn-primary">Reset</a>
                                </div>

                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-end">
                                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3">
                                    <li class="nav-item">
                                   <a href="{{ route('exportOrderReport') }}" class="nav-link px-3 py-2" 
                                       style="border: none; background: #40518924; cursor: pointer; text-decoration:none;">
                                       <i class="ri-download-line me-1 align-bottom"></i> Download Excel
                                    </a>

                                    </li>
                                </ul>
                            </div>
                                
                                <div class="table-responsive table-card mb-1">
                                    <table class="table table-nowrap align-middle" id="orderTable">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th >S.No</th>
                                                <th >Order ID</th>
                                                <th>Seller</th>
                                                <th>Shop Name </th>
                                                <th>Staff </th>
                                                <th >Order Date</th>
                                                <!--<th >Delivery Date</th>-->
                                                <th>Amount</th>
                                                <!--<th >Payment Method</th>-->
                                                <th >Status</th>
                                                <th >Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @if(sizeof($orderList))
                                            @php
                                              $i=1;
                                            @endphp
                                            @foreach($orderList  as $order)
                                            <tr>
                                                <td >{{$orderList->firstItem() + $loop->index }}</td>
                                                <td class="id"><a class="fw-medium link-primary">{{isset($order->order_id)?$order->order_id:''}}</a></td>
                                                <td class="customer_name">{{isset($order->seller->name)?$order->seller->name:''}}</td>
                                                <td class="product_name">{{isset($order->seller->shop_name)?$order->seller->shop_name:''}}</td>
                                                <td class="product_name">{{isset($order->staff->name)?$order->staff->name:''}}</td>
                                                <td class="date">{{ isset($order->date) ? \Carbon\Carbon::parse($order->date)->format('d-m-Y') : '' }}</td>
                                                <!--<td class="date">{{ isset($order->delivery_date) ? \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y') : '' }}</td>-->
                                                <td class="amount">{{config('constants.INDIAN_RUPEE_SYMBOL').' '.$order->total_price}}</td>
                                                 @php
                                                     $statusLabels = [
                                                        'All Orders' => '',
                                                         0 => 'Pending',
                                                         1 => 'To Delivered',
                                                         2 => 'Pickup',
                                                         3 => 'Delivered',
                                                         4 => 'Cancel',
                                                         5 => 'Return',
                                                         6 => 'Assign',
                                                      ];
                                                    
                                                      $statusClasses = [
                                                        'bg-warning-subtle text-warning',    // Pending
                                                        'bg-secondary-subtle text-secondary',    // To Delivered
                                                        'bg-info-subtle text-info',          // Pickup
                                                        'bg-success-subtle text-success',     // Delivered
                                                        'bg-danger-subtle text-danger',     // Cancel
                                                        'bg-danger-subtle text-danger',   // Return
                                                        'bg-primary-subtle text-primary',     // Assign
                                                      ];
                                                    $status = $order->status ?? 0;
                                                @endphp
                                                
                                                <td class="status">
                                                    <span class="badge {{ $statusClasses[$status] ?? 'bg-warning-subtle text-warning' }} text-uppercase">
                                                        {{ $statusLabels[$status] ?? 'Pending' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <ul class="list-inline hstack gap-2 mb-0">
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="View" data-bs-original-title="View">
                                                            <a href="@encryptedRoute('orderDetailReport',$order->id)" class="btn btn-primary btn-sm d-inline-block">
                                                                <!--<i class="ri-eye-fill fs-16"></i>-->
                                                                View 
                                                            </a>
                                                             
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                                <div class="noresult" style="display: none">
                                                    <div class="text-center">
                                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon>
                                                        <h5 class="mt-2">Sorry! No Result Found</h5>
                                                        <p class="text-muted">We've searched more than 150+ Orders We did not find any orders for you search.</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </tbody>
                                    </table>
                                     @if(count($orderList)==0)
                                    <div class="noresult" style="display: none">
                                        <div class="text-center">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon>
                                            <h5 class="mt-2">Sorry! No Result Found</h5>
                                            <p class="text-muted">We've searched more than 150+ Orders We did not find any orders for you search.</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="pagination-wrap hstack gap-2">
                                      {{ $orderList->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end col-->
            </div>
            <!--end row-->

        </div>
        <!-- container-fluid -->
    </div>
@endsection