@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
     <div class="row">
         <div class="col-lg-12">
            <div class="card">
             
               <!-- end card header -->
              <div class="card-body">
                  <div class="live-preview">
                     <form  method="POST" action="{{route('billTransactionReport')}}">
                         @csrf
                         @method('POST')
                        <div class="row row-cols-lg-auto g-3 align-items-center">
                             
                           <div class="col-12">
                               <label class="form-label">Choose Seller</label>
                              <div class="input-group">
                                  
                                   <select class="form-select" data-choices="" data-choices-sorting="true" name="seller_id" id="seller_id" >
                                                <option value="">Select Seller</option>
                                                @foreach($seller as $seller_data)
                                                    <option value="{{ $seller_data->id }}" {{ request('seller_id') == $seller_data->id ? 'selected' : '' }}>
                                                        {{ $seller_data->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                              </div>
                           </div>
                           
                           <div class="col-12">
                               <label class="form-label">Choose Staff</label>
                              <div class="input-group">
                                   <select class="form-select" data-choices="" data-choices-sorting="true" name="staff_id" id="staff_id" >
                                                <option value="">Select Staff</option>
                                                @foreach($user as $user_data)
                                                    <option value="{{ $user_data->id }}"  {{ request('staff_id') == $user_data->id ? 'selected' : '' }}>
                                                        {{ $user_data->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                              </div>
                           </div>
                           
                             
                            <div class="col-12">
                                <label class="form-label">Start Date</label>
                                <div class="input-group">
                                    <input type="date" name="start_date"  value="{{ request('start_date') }}" class="form-control" id="start_date">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">End Date</label>
                                <div class="input-group">
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" id="end_date">
                                </div>
                            </div>
                            
                             <div class="col-12">
                                   <label class="form-label">Payment Status</label>
                                   <div class="input-group">
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="payment_status" id="payment_status">
                                 <option value =''>Select Status</option>
                               <option value="0" {{ old('payment_status', request('payment_status')) == '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ old('payment_status', request('payment_status')) == '1' ? 'selected' : '' }}>Remaining</option>
                                <option value="2" {{ old('payment_status', request('payment_status')) == '2' ? 'selected' : '' }}>Complete</option>
                              </select>
                              </div>
                           </div>
                           
                              <div class="col-12">
                                   <label class="form-label">Payment Mode</label>
                                   <div class="input-group">
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="payment_mode" id="payment_mode">
                                 <option value =''>Select Status</option>
                                 <option value="1" {{ old('payment_mode', request('payment_mode')) == '1' ? 'selected' : '' }}>Cash</option>
                                 <option value="2" {{ old('payment_mode', request('payment_mode')) == '2' ? 'selected' : '' }}>Cheque</option>
                                 <option value="3" {{ old('payment_mode', request('payment_mode')) == '3' ? 'selected' : '' }}>UPI</option>
                              </select>
                              </div>
                           </div>
                           
                           <div class="col-12">
                              <button type="submit" class="btn btn-success">Submit</button>
                               <a href="{{route('billTransactionReport')}}" class="btn btn-primary">Reset</a>
                              
                           </div>
                           
                           <!--end col-->
                        </div>
                        <!--end row-->
                     </form>
                  </div>
               </div>
               <!--end card-body-->
            </div>
            <!--end card-->
         </div>
         <!-- end col -->
      </div>
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header">
                  <h4 class="card-title mb-0">Bill Transactions</h4>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="" id="">
                     <div class="table-responsive table-card mt-3 mb-1">
                       <table class="table align-middle table-nowrap">
    <thead class="table-light">
        <tr>
            <th>S No</th>
            <th>Order No.</th>
            <th>Transaction No.</th>
            <th>Seller</th>
            <th>Staff</th>
            <th>Date</th>
            <th>Payment Mode</th>
            <th>Amount</th>
            <th>Remaining Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="list form-check-all">
        @foreach ($transactions as $index => $transaction)
            <tr>
                <td>{{ $transactions->firstItem() + $index }}</td>
                <td>{{ $transaction->order->order_id }}</td>
                <td> {{ $transaction->transaction_no }}</td>
               
                <td>{{ $transaction->seller->name ?? 'N/A' }}</td>
                <td>{{ $transaction->staff->name ?? 'N/A' }}</td>
              <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
                <td>
                    @if($transaction->payment_mode == 1)
                        Cash
                    @elseif($transaction->payment_mode == 2)
                        Cheque
                    @elseif($transaction->payment_mode == 3)
                        UPI
                    @endif
                </td>
                <td>{{ number_format($transaction->amount, 2) }}</td>
                <td>{{ number_format($transaction->deduct_amount, 2) }}</td>
                <td>
                    @if($transaction->status == 0)
                        <span class="badge bg-danger">Pending</span>
                    @elseif($transaction->status == 1)
                        <span class="badge bg-warning">Remaining</span>
                    @elseif($transaction->status == 2)
                    <span class="badge bg-success">Completed</span>
                    @else
                        <span class="badge bg-danger">Other</span>
                    @endif
                </td>
                <td>
                    <a href="@encryptedRoute('billTransactionHistory',$transaction->id)" class="btn btn-sm btn-primary">View</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $transactions->links('pagination::bootstrap-5') }}


                     

                        <div class="noresult" style="display: none">
                           
                           <div class="text-center">
                              <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                              <h5 class="mt-2">Sorry! No Result Found</h5>
                              <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders for you search.</p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- end card -->
            </div>
            <!-- end col -->
         </div>
         <!-- end col -->
      </div>
   </div>
</div>

<script>
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function () {
        endDateInput.min = this.value; 
    });

    endDateInput.addEventListener('change', function () {
        if (endDateInput.value < startDateInput.value) {
            alert("End date cannot be earlier than start date!");
            endDateInput.value = ""; 
        }
    });
</script>
@endsection