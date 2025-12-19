@extends('admin.layout.layout')
@section('content')

<div class="page-content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">Expense Filter</h4>
                  <div class="col-sm-auto">
                          
                        </div>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="live-preview">
                     <form  method="POST" action="{{ route('expenselist') }}">
                         @csrf
                         @method('POST')
                        <div class="row row-cols-lg-auto g-3 align-items-center">
                          
                           <div class="col-12">
                              <div class="input-group">
                                 <input type="date" name="expense_date" class="form-control" id="inlineFormInputGroupUsername" placeholder="Expense Date" value="{{ request('expense_date') }}">
                              </div>
                           </div>
                        
                           <div class="col-12">
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="status" id="Status">
                                 <option value =''>Select Status</option>
                                 <option value="1" {{ old('status', request('status')) == '1' ? 'selected' : '' }}>Approved</option>
                                 <option value="0" {{ old('status', request('status')) == '0' ? 'selected' : '' }}>Pending</option>
                                 <option value="2" {{ old('status', request('status')) == '2' ? 'selected' : '' }}>Reject</option>
                              </select>
                           </div>
                           <!--end col-->
                           <div class="col-12">
                              <button type="submit" class="btn btn-success">Submit</button>
                           </div>
                            <div class="col-12">
                              <a href="{{ route('expenselist') }}" class="btn btn-primary">Reset</a>
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
                  <h4 class="card-title mb-0">Expense List</h4>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="" id="">
                     <div class="row g-4 mb-3">
                        <div class="col-sm">
                        </div>
                     </div>
                     <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle table-nowrap" id="">
                           <thead class="table-light">
                              <tr>
                                 <th scope="col" >
                                    S No
                                 </th>
                                 <th class="" data-sort="name">Name</th>
                                  <th class="" data-sort="">Expense Date</th>
                                  <th class="" data-sort="">Expense Amount</th>
                                  <th class="" data-sort="">Expense Image</th>
                                  <th class="" data-sort="">Remark</th>
                                 <th class="" data-sort="name">status</th>
                                 <th class="" >Action</th>
                              </tr>
                           </thead><tbody class="list form-check-all">
                              @if(sizeof($getexpenseLists))
                              @php
                              $i=1;
                              @endphp
                              @foreach($getexpenseLists as $expense)
                                @php
                                    $startDate = \Carbon\Carbon::parse($expense->start_date);
                                    $endDate = \Carbon\Carbon::parse($expense->end_date);
                                    $days = $startDate->diffInDays($endDate) + 1;
                                @endphp
                              <tr>
                                 <td class="id" style="">{{ $getexpenseLists->firstItem() + $loop->index }}</td>
                                 <td class="customer_name">{{isset($expense->User->name)?$expense->User->name:''}}</td>
                                 <td class="customer_name">{{isset($expense->expense_date)?$expense->expense_date:''}}</td>
                                 <td class="customer_name">{{isset($expense->expense_amount)?$expense->expense_amount:''}}</td>
                                 <td class="customer_name">@if(!empty($expense->expense_image))
                                 <a href="{{asset($expense->expense_image)}}" target="_blank">
                                      <img src="{{asset($expense->expense_image)}}" height="40px" width="40px">
                                      </a>  @else  N/A @endif</td>
                                 <td class="customer_name">{{isset($expense->remark)?$expense->remark:''}}</td>
                                @if($expense->status == 1)
                                    <td class="customer_name"> <span class="badge bg-success">Approve</span></td>
                                @elseif($expense->status == 0)
                                    <td class="customer_name"> <span class="badge bg-primary">Pending</span></td>
                                @else
                                    <td class="customer_name"> <span class="badge bg-danger">Reject</span></td>
                                @endif
                                 
                                 
                                 <td>
                                    <div class="d-flex gap-2">
                                       <div class="edit">
                                          @php
                                          $id= \App\Utils\Crypto::encryptId($expense->id);
                                          @endphp
                                          
                                       </div>
                                      

                                       @if($expense->status ==0)
                                       <div class="status">
                                         @if($expense->status == 1)
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the user status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="{{ route('expensestatusupdate', [$id, 0]) }}"
                                           class="btn btn-sm btn-success sweet-confirm">
                                           <i class="ri-checkbox-circle-fill"></i>
                                        </a>
                                        @else
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the user status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="{{ route('expensestatusupdate', [$id, 1]) }}"
                                           class="btn btn-sm btn-primary sweet-confirm">
                                           <i class="ri-indeterminate-circle-fill"></i>
                                        </a>
                                        @endif
                                       </div>
                                       @endif
                                        @if($expense->status ==0 )
                                       <div class="status">
                                         <a href="javascript:void(0)"
                                           data-title="Are you sure you want to delete this staff?"
                                           data-text="This action cannot be undone!"
                                           data-confirm="Yes, delete it!"
                                           data-success="Deleted successfully!"
                                           data-href="{{ route('expensestatusupdate', [$id,'2']) }}"
                                           class="btn btn-sm btn-danger sweet-confirm">
                                           <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                       </div>
                                        @endif
                                       
                                       
                                    </div>
                                 </td>
                              </tr>
                              @endforeach
                              @else
                              <tr>
                                  <td colspan="6"> No Data Found !!</td>
                                  </tr>
                              @endif
                           </tbody>
                        </table>
                         {{ $getexpenseLists->links('pagination::bootstrap-5') }}
                        
                        <div class="noresult" style="display: none">
                           <div class="text-center">
                              <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                              <h5 class="mt-2">No Result Found</h5>
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


@endsection