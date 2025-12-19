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
                                    <h5 class="card-title mb-0">Rider Bill Settlement Request List</h5>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button type="button" class="btn btn-info"><i class="ri-file-download-line align-bottom me-1"></i> Import</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body border border-dashed border-end-0 border-start-0 mb-4">
                            <form action="{{route('riderBillList')}}" method='post'>
                                @csrf
                                @method('POST')
                                <div class="row g-3">
                                    
                                    <!--end col-->
                                    <div class="col-xxl-2 col-sm-6">
                                        <div>
                                            <input type="text" name= "date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true" id="datepicker" placeholder="Select clear date" value="{{ request('date') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xxl-2 col-sm-4">
                                        <div>
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
                                  
                                  <div class="col-xxl-1 col-sm-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <a href="{{ route('riderBillList') }}" class="btn btn-primary">Reset</a>
                                </div>

                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <div class="card-body pt-0">
                            <div>
                          

                                <div class="table-responsive table-card mb-1">
                                    <table class="table table-nowrap align-middle" id="orderTable">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th >S.No</th>
                                                 <th>Order Id</th>
                                                <th>Seller</th>
                                                <th>Rider </th>
                                                <th>Amount</th>
                                                <th >Payment Mode</th>
                                                  <th >Date</th>
                                                <th >Status</th>
                                                <th >Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @if(sizeof($orderList))
                                            @php
                                              $i=1;
                                            @endphp
                                            @foreach($orderList as $key => $order)
                                            <tr>
                                                <td >{{ ++$key}}</td>
                                                <td class="customer_name">{{isset($order->order->order_id)?$order->order->order_id:''}}</td>
                                                <td class="customer_name">{{isset($order->seller->name)?$order->seller->name:''}}</td>
                                                 <td class="customer_name">{{isset($order->rider->name)?$order->rider->name:''}}</td>
                                               
                                                <td class="amount">{{config('constants.INDIAN_RUPEE_SYMBOL').' '.$order->amount}}</td>
                                                  @if($order->payment_mode == 1)
                                                    <td class="status">
                                                        <span class="badge bg-primary-subtle text-primary text-uppercase">
                                                            Cash
                                                        </span>
                                                    </td>
                                                @endif
                                                 @if($order->payment_mode == 3)
                                                    <td class="status">
                                                        <span class="badge bg-primary-subtle text-primary text-uppercase">
                                                            UPI
                                                        </span>
                                                    </td>
                                                @endif
                                                
                                             <td class="customer_name">    {{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d-m-Y h:i A') : '' }}  </td>

                                                
                                                 @php
                                                    $statusLabels = ['Pending', 'Approved','Reject'];
                                                    $statusClasses = [
                                                        'bg-warning-subtle text-warning',    
                                                        'bg-success-subtle text-success',    
                                                        'bg-primary-subtle text-primary',    
                                                        'bg-danger-subtle text-danger',      
                                                    ];
                                                   
                                                    $status = $order->status ?? 0;
                                                    $paymentMode = $order->payment_mode ?? 1
                                                @endphp
                                                
                                                <td class="status">
                                                    <span class="badge {{ $statusClasses[$status] ?? 'bg-warning-subtle text-warning' }} text-uppercase">
                                                        {{ $statusLabels[$status] ?? 'Pending' }}
                                                    </span>
                                                </td>
                                                
                                                
                                                <td>
                                    <div class="d-flex gap-2">
                                            <div class="status">
                                               @if($order->status ==0)
                                                    <a href="javascript:void(0)"
                                                       data-title="Change Status?"
                                                       data-text="Are you sure you want to Approved this Bill?"
                                                       data-confirm="Yes, change it!"
                                                       data-success="Approved successfully!"
                                                       data-href="@encryptedRoute('approveriderBill',$order->id,1)"
                                                       class="btn btn-sm btn-success sweet-confirm">
                                                       <i class="ri-checkbox-circle-fill"></i>
                                                    </a>
                                                @endif
                                             
                                            </div>

                                    <!--@if($order->status ==0 )-->
                                    <!--   <div class="status">-->
                                    <!--         <a href="javascript:void(0)"-->
                                    <!--           data-title="Are you sure you want to Reject this Bill?"-->
                                    <!--           data-text="This action cannot be undone!"-->
                                    <!--           data-confirm="Yes, change it!"-->
                                    <!--           data-success="Reject successfully!"-->
                                    <!--           data-href="@encryptedRoute('approveriderBill',$order->id,2)"-->
                                    <!--           class="btn btn-sm btn-danger sweet-confirm">-->
                                    <!--           <i class="ri-delete-bin-2-line"></i>-->
                                    <!--        </a>-->
                                    <!--   </div>-->
                                    <!--@endif-->
                                       
                                       
                                    </div>
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
                                @if(sizeof($orderList))
                                <div class="d-flex justify-content-end">
                                    <div class="pagination-wrap hstack gap-2">
                                      {{ $orderList->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                                @endif
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