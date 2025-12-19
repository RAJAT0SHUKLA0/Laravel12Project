<div class="table-responsive table-card mb-5 mt-3">
    <table class="table table-nowrap align-middle" id="orderTable">
        <thead class="text-muted table-light">
            <tr class="text-uppercase">
                <th ><input type="checkbox" id="selectAll"></th>
                <th >Order ID</th>
                <th>Seller</th>
                <th>Shop Name </th>
                <th>Staff </th>
                <th >Order Date</th>
                <th >Delivery Date</th>
                <th>Amount</th>
                <th >Status</th>
            </tr>
        </thead>
        <tbody class="list form-check-all">
            @if(sizeof($orderList))
            @php
              $i=1;
            @endphp
            @foreach($orderList  as $order)
            <tr>
                <td ><input type="checkbox" id="selectOrder" class="item-checkbox" name="order_id[]"value="{{isset($order->id)?$order->id:''}}"></td>
                <td class="id"><a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">{{isset($order->order_id)?$order->order_id:''}}</a></td>
                <td class="customer_name">{{isset($order->seller->name)?$order->seller->name:''}}</td>
                <td class="product_name">{{isset($order->seller->shop_name)?$order->seller->shop_name:''}}</td>
                <td class="product_name">{{isset($order->staff->name)?$order->staff->name:''}}</td>
                <td class="date">{{ isset($order->date) ? \Carbon\Carbon::parse($order->date)->format('d-m-Y') : '' }}</td>
                <td class="date">{{ isset($order->delivery_date) ? \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y') : '' }}</td>
                <td class="amount">{{config('constants.INDIAN_RUPEE_SYMBOL').' '.$order->total_price}}</td>
                 @php
                    $statusLabels = ['Pending', 'Approved', 'Pickup', 'Deliver', 'Return', 'Cancel'];
                    $statusClasses = [
                        'bg-warning-subtle text-warning',    
                        'bg-primary-subtle text-primary',    
                        'bg-info-subtle text-info',          
                        'bg-success-subtle text-success',    
                        'bg-secondary-subtle text-secondary',
                        'bg-danger-subtle text-danger',      
                    ];
                    $status = $order->status ?? 0;
                @endphp
                
                <td class="status">
                    <span class="badge {{ $statusClasses[$status] ?? 'bg-warning-subtle text-warning' }} text-uppercase">
                        {{ $statusLabels[$status] ?? 'Pending' }}
                    </span>
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
      <div class="d-flex justify-content-end">
                                    <div class="pagination-wrap hstack gap-2">
                                      {{ $orderList->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
</div>
