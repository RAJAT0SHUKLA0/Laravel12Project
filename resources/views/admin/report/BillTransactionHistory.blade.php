@extends('admin.layout.layout')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Bill Transaction History</h4>
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
            <th>Deduct Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaction_history as $index => $history)
            <tr>
                <td>{{ $transaction_history->firstItem() + $index }}</td>

                <td>{{ $history->transaction->order->order_id ?? 'N/A' }}</td>

                <td>{{ $history->transaction->transaction_no ?? 'N/A' }}</td>

                <td>{{ $history->seller->name ?? 'N/A' }}</td>

                <td>{{ $history->staff->name ?? 'N/A' }}</td>

                <td>{{ \Carbon\Carbon::parse($history->date)->format('d-m-Y') }}</td>

                <td>
                    @if ($history->payment_mode == 1)
                        Cash
                    @elseif ($history->payment_mode == 2)
                        Cheque
                    @elseif ($history->payment_mode == 3)
                        UPI
                    @else
                        Other
                    @endif
                </td>

                <td>{{ number_format($history->deduct_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $transaction_history->links('pagination::bootstrap-5') }}

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

@endsection
