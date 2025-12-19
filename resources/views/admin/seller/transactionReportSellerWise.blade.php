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
                                    <h5 class="card-title mb-0">Transaction History</h5>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button type="button" class="btn btn-info"><i class="ri-file-download-line align-bottom me-1"></i> Import</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body pt-0">
                            <div>
                                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                                  @php
                                    $statusLabels = ['Cash', 'Cheque', 'Upi'];
                                    $iconClass = [
                                        'ri-store-2-fill me-1',
                                        'ri-checkbox-circle-line',
                                        'ri-checkbox-circle-line', 
                                    ];
                                @endphp
                                
                                @if(count($statusLabels))
                                    <ul class="nav">
                                        @foreach($statusLabels as $key => $status)
                                            <li class="nav-item">
                                                <form action="{{ route('order') }}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="status" value="{{ $status }}">
                                                    <button type="submit"
                                                            class="nav-link px-3 py-2 {{ (request('status') === null && $status === 'Cash') || $status === request('status') ? 'active' : '' }}"
                                                            style="border: none; background: none; cursor: pointer;">
                                                        <i class="{{ $iconClass[$key] ?? '' }} me-1 align-bottom"></i>{{ $status }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                </ul>

                                <div class="table-responsive table-card mb-1">
                                    <table class="table table-nowrap align-middle" id="orderTable">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th >S.No</th>
                                                <th >Transaction ID</th>
                                                <th>Seller</th>
                                                <th>Staff </th>
                                                <th > Date</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @if(sizeof($TransactionReport))
                                            @php
                                              $i=1;
                                            @endphp
                                            @foreach($TransactionReport  as $order)
                                            <tr>
                                                <td >{{$TransactionReport->firstItem() + $loop->index }}</td>
                                                <td class="id"><a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">{{isset($order->transaction->transaction_no)?$order->transaction->transaction_no:''}}</a></td>
                                                <td class="customer_name">{{isset($order->seller->name)?$order->seller->name:''}}</td>
                                                <td class="product_name">{{isset($order->staff->name)?$order->staff->name:''}}</td>
                                                <td class="date">{{ isset($order->date) ? \Carbon\Carbon::parse($order->date)->format('d-m-Y') : '' }}</td>
                                                <td class="product_name">{{isset($order->deduct_amount)?$order->deduct_amount:''}}</td>

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
                                     @if(count($TransactionReport)==0)
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
                                      {{ $TransactionReport->links('pagination::bootstrap-5') }}
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