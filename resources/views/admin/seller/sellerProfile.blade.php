@extends('admin.layout.layout')
@section('content')
    @php
        $address = \App\Utils\Uploads::getAddressFromCoordinates($profileData->latitude, $profileData->longitude);
    @endphp
    <div class="page-content">
        <div class="container-fluid">
            <div class="profile-foreground position-relative mx-n4 mt-n4">
                <div class="profile-wid-bg">
                    <img src="" alt="" class="profile-wid-img">
                </div>
            </div>
            <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
                <div class="row g-4">
                    <div class="col-auto">
                        <div class="avatar-lg">
                            <img src="{{ asset('storage/uploads/profile/' . $profileData->profile_pic) }}" alt="user-img"
                                class="img-thumbnail">
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col">
                        <div class="">
                            <h3 class="text-white mb-1">{{ isset($profileData->name) ? $profileData->name : '' }}</h3>
                            <p class="text-white text-opacity-75">Seller</p>
                            <p class="text-white text-opacity-75">+(91)
                                {{ isset($profileData->mobile) ? $profileData->mobile : '' }}</p>
                            <div class="hstack text-white-50 gap-1">
                                <div class="me-2"><i
                                        class="ri-map-pin-user-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $address }}
                                </div>
                                <!--<div>-->
                                <!--    <i class="ri-building-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>Themesbrand-->
                                <!--</div>-->
                            </div>
                        </div>
                    </div>

                </div>
                <!--end row-->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <div class="d-flex profile-wrapper">
                            <!-- Nav tabs -->
                            <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab"
                                        role="tab" aria-selected="true">
                                        <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span
                                            class="d-none d-md-inline-block">Overview</span>
                                    </a>
                                </li>
                                @if (sizeof($profileData->transaction))
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link fs-14" data-bs-toggle="tab" href="#documents" role="tab"
                                            aria-selected="false" tabindex="-1">
                                            <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span
                                                class="d-none d-md-inline-block">Bill Settlement</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <!-- Tab panes -->
                        <div class="tab-content pt-4 text-muted">
                            <div class="tab-pane active" id="overview-tab" role="tabpanel">
                                <div class="row">
                                    <!--<div class="col-xl-4">-->
                                    <!--    <div class="card">-->
                                    <!--        <div class="card-header">-->
                                    <!--            <h4 class="card-title mb-0">Order Chart</h4>-->
                                    <!--        </div>-->

                                    <!--        <div class="card-body">-->
                                    <!--            <div id="Order"></div>-->


                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-xl-4">-->
                                    <!--    <div class="card">-->
                                    <!--        <div class="card-header">-->
                                    <!--            <h4 class="card-title mb-0">Order Product Chart</h4>-->
                                    <!--        </div>-->

                                    <!--        <div class="card-body">-->
                                    <!--            <div id="Product"></div>-->



                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-xl-4">-->
                                    <!--    <div class="card">-->
                                    <!--        <div class="card-header">-->
                                    <!--            <h4 class="card-title mb-0">Transaction History</h4>-->
                                    <!--        </div>-->

                                    <!--        <div class="card-body">-->
                                    <!--            <div id="TxnStatus"></div>-->



                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="row">-->
                                    <!--    <div class="col-xl-4">-->
                                    <!--        <div class="card">-->
                                    <!--            <div class="card-header">-->
                                    <!--                <h4 class="card-title mb-0">Transaction Payment History</h4>-->
                                    <!--            </div>-->

                                    <!--            <div class="card-body">-->
                                    <!--                <div id="TransactionAmount"></div>-->



                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="row">
                                        <div class="col-xxl-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Orders</h5>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-borderless align-middle mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th scope="col">S No</th>
                                                                            <th scope="col">Order No</th>
                                                                            <th scope="col">Seller Name</th>
                                                                            <th scope="col">Shop Name</th>
                                                                            <th scope="col">Staff Name</th>
                                                                            <th scope="col">Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if (sizeof($profileData->order))
                                                                            @php $i = 1; @endphp
                                                                            @foreach ($profileData->order as $order)
                                                                                <tr>
                                                                                    <td>{{ $i++ }}</td>
                                                                                    <td><a href="@encryptedRoute('orderDetails',$order->id)">  {{ $order->order_id ?? '' }}</a></td>
                                                                                    <td>{{ $order->seller->name ?? '' }}
                                                                                    </td>
                                                                                    <td>{{ $order->seller->shop_name ?? '' }}
                                                                                    </td>
                                                                                    <td>{{ $order->staff->name ?? '' }}</td>
                                                                                    @php
                                                                                        $statusLabels = [
                                                                                            'Pending',
                                                                                            'Approved',
                                                                                            'Pickup',
                                                                                            'Deliver',
                                                                                            'Return',
                                                                                            'Cancel',
                                                                                        ];
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
                                                                                        <span
                                                                                            class="badge {{ $statusClasses[$status] ?? 'bg-warning-subtle text-warning' }} text-uppercase">
                                                                                            {{ $statusLabels[$status] ?? 'Pending' }}
                                                                                        </span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end card body -->
                                            </div>
                                            <!-- end card -->
                                        </div>
                                        <!--end col-->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="documents" role="tabpanel">
                                <form action="{{ route('billSettlement') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="card">
                                        @if (sizeof($profileData->transaction))
                                            @php $i = 1; @endphp
                                            <div class="card-body ">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div class="">
                                                        <label for="validationTooltip01" class="form-label">
                                                            Amount</label>
                                                        <input type="text" class="form-control"
                                                            id="validationTooltip01" name="amount" value="@if(!empty($ChequeInfoData)) {{$ChequeInfoData->amount}}@endif"
                                                            required="">
                                                        <div class="invalid-tooltip">
                                                            Amount is required.
                                                        </div>
                                                        <input type="hidden" value="@encryptedRoute('sellerprofile', $profileData->id)" name="seller_id">
                                                    </div>
                                                </div>
                                                @if(!empty($ChequeInfoData)) <input type="hidden" value="{{$ChequeInfoData->id}}" name="cheque_id"> @else <input type="hidden" value="0" name="cheque_id">   @endif
                                                
                                                
                                                <label for="validationTooltip01" class="form-label">Payment Mode</label>
                                                <div class="d-flex align-items-center mb-4">
                                                    <div class="form-check form-radio-outline form-radio-dark me-4">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_mode" id="formradioCash" value="1"
                                                            >
                                                        <label class="form-check-label" for="formradioCash">Cash</label>
                                                    </div>
                                                    
                                                    <div class="form-check form-radio-outline form-radio-dark me-4">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_mode" id="formradioCheque" value="2"  @if(!empty($ChequeInfoData))  checked @endif>
                                                        <label class="form-check-label"
                                                            for="formradioCheque">Cheque</label>
                                                    </div>
                                                   
                                                    
                                                    <div class="form-check form-radio-outline form-radio-dark me-4">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_mode" id="formradioCheque" value="3">
                                                        <label class="form-check-label" for="formradioCheque">Upi</label>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-borderless align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th scope="col"></th>
                                                                        <th scope="col">S No</th>
                                                                        <th scope="col">Bill No</th>
                                                                        <th scope="col">Remaining Amount</th>
                                                                        <th scope="col">Date</th>
                                                                        <th scope="col">Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($profileData->transaction as $key => $transaction)
                                                                        <tr>
                                                                            <td>
                                                                                <input type="checkbox"
                                                                                    value="{{ $transaction->id }}"
                                                                                    name="bill_id[]"
                                                                                   {{ $key == 0 ? 'checked' : '' }}>
                                                                            </td>
                                                                            <td>
                                                                                {{ $i++ }}
                                                                            </td>
                                                                            <td>{{ $transaction->transaction_no }}</td>
                                                                            <td>{{ $transaction->deduct_amount }}</td>
                                                                            <td>{{ $transaction->date }}</td>
                                                                            @php
                                                                                $statusLabels = [
                                                                                    'Pending',
                                                                                    'remaining',
                                                                                    'completed',
                                                                                ];
                                                                                $statusClasses = [
                                                                                    'bg-warning-subtle text-warning',
                                                                                    'bg-primary-subtle text-primary',
                                                                                    'bg-success-subtle text-success',
                                                                                ];
                                                                                $status = $transaction->status ?? 0;
                                                                            @endphp
                                                                            <td class="status">
                                                                                <span
                                                                                    class="badge {{ $statusClasses[$status] ?? 'bg-warning-subtle text-warning' }} text-uppercase">
                                                                                    {{ $statusLabels[$status] ?? 'Pending' }}
                                                                                </span>
                                                                            </td>
                                                                            <!--<td class="">-->
                                                                            <!--  <a href="@encryptedRoute('transactionReportSellerWise', $transaction->seller_id)" class="btn btn-outline-warning btn-icon waves-effect waves-light material-shadow-none"><i class="ri-menu-2-line"></i></a>-->
                                                                            <!--</td>-->
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button class="btn btn-primary" type="submit">Submit</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <!--end tab-content-->
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
