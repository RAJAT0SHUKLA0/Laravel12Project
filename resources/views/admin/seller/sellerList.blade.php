@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">Seller Filter</h4>
                  <div class="col-sm-auto">
                           <div>
                              <a href="{{route('sellerAdd')}}"  class="btn btn-success add-btn" ><i class="ri-add-line align-bottom me-1"></i> Add</a>
                           </div>
                        </div>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="live-preview">
                     <form  method="POST" action="{{ route('sellerlist') }}">
                         @csrf
                         @method('POST')
                        <div class="row row-cols-lg-auto g-3 align-items-center">
                             <div class="col-12">
                              <div class="input-group">
                                 <input type="text" class="form-control" name="seller_id" id="inlineFormInputGroupUsername" placeholder="Seller Id" value="{{ request('seller_id') }}">
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="input-group">
                                 <input type="text" name="name" class="form-control" id="inlineFormInputGroupUsername" placeholder="Name" value="{{ request('name') }}">
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="input-group">
                                 <input type="text" name="mobile" class="form-control" id="inlineFormInputGroupUsername" placeholder="Mobile" value="{{ request('mobile') }}">
                              </div>
                           </div>
                           <!--end col-->
                           <div class="col-12">
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="status" id="Status">
                                 <option value =''>Select Status</option>
                                 <option value="1" {{ old('status', request('status')) == '1' ? 'selected' : '' }}>Active</option>
                                 <option value="0" {{ old('status', request('status')) == '0' ? 'selected' : '' }}>In Active</option>
                              </select>
                           </div>
                           <!--end col-->
                           
                           
                           
                           <div class="col-12">
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="state_id" id="state" onchange="getCity(this.value,'{{route('getCity')}}','{{ csrf_token() }}')">
                                 <option value =''>Select State</option>
                                 @if(sizeof($getState))
                                     @foreach($getState as $state)
                                       <option value="{{isset($state->id)?$state->id:''}}" {{ isset($state->id) && old('status', request('state_id')) == $state->id ? 'selected' : '' }}>{{isset($state->name)?$state->name:''}}</option>
                                     @endforeach
                                 @endif
                              </select>
                           </div>
                           <div class="col-12">
                              <select class="form-select appendcity" data-choices="" data-choices-sorting="true" name="city_id" id="city">
                                 <option value =''>Select City</option>
                               @if(request()->has('city_id') && sizeof($getAllCity))
                                     @foreach($getAllCity as $city)
                                       <option value="{{isset($city->id)?$city->id:''}}" {{ isset($city->id) && old('status', request('city_id')) == $city->id ? 'selected' : '' }}>{{isset($city->name)?$city->name:''}}</option>
                                     @endforeach
                                 @endif
                              </select>
                           </div>
                           
                           <div class="col-12">
                              <button type="submit" class="btn btn-success">Submit</button>
                           </div>
                            <div class="col-12">
                              <a href="{{ route('sellerlist') }}" class="btn btn-primary">Reset</a>
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
                  <h4 class="card-title mb-0">Seller List</h4>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="listjs-table" id="customerList">
                     <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle table-nowrap" id="customerTable">
                           <thead class="table-light">
                              <tr>
                                 <th scope="col" >
                                    S No
                                 </th>
                                 <th class="" data-sort="name">Name</th>
                                 <th class="" data-sort="email">email</th>
                                 <th class="" data-sort="mobile">phone</th>
                                 <th class="" data-sort="staff_id">Seller Id</th>
                                 <th class="" data-sort="name">status</th>
                                 <th class="" data-sort="state_id">state</th>
                                 <th class="" data-sort="city_id">city</th>
                                 <th class="" >Action</th>
                              </tr>
                           </thead>
                           <tbody class="list form-check-all">
                              @if(sizeof($getStaffList1))
                              @php
                              $i=1;
                              @endphp
                              @foreach($getStaffList1 as $staff)
                              <tr>
                                 <td class="id" >{{ $getStaffList1->firstItem() + $loop->index }}</td>
                                 <td class="customer_name">{{isset($staff->name)?$staff->name:''}}</td>
                                 <td class="customer_name">{{isset($staff->email)?$staff->email:''}}</td>
                                 <td class="customer_name">{{isset($staff->mobile)?$staff->mobile:''}}</td>
                                 
                                 <td class="customer_name">{{isset($staff->seller_id)?$staff->seller_id:''}}</td>
                                 
                                 @if($staff->status ==1)
                                 <td class="customer_name"> <span class="badge bg-success">Active</span></td>
                                 @else
                                 <td class="customer_name"> <span class="badge bg-primary">In Active</span></td>
                                 @endif
                                 <td class="customer_name">{{isset($staff->state->name)?$staff->state->name:''}}</td>
                                 <td class="customer_name">{{isset($staff->city->name)?$staff->city->name:''}}</td>
                                 <td>
                                    <div class="d-flex gap-2">
                                       <div class="edit">
                                         
                                          <a href="@encryptedRoute('sellerEdit', $staff->id)" class="btn btn-sm btn-success"><i class="ri-pencil-line"></i></a>
                                       </div>
                                      
                                       <div class="status">
                                        @if($staff->status == 1)
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the seller status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="@encryptedRoute('sellerstatusUpdate', $staff->id, 0)"
                                           class="btn btn-sm btn-success sweet-confirm">
                                           <i class="ri-checkbox-circle-fill"></i>
                                        </a>
                                        @else
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the seller status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href=" @encryptedRoute('sellerstatusUpdate', $staff->id, 1)"
                                           class="btn btn-sm btn-primary sweet-confirm">
                                           <i class="ri-indeterminate-circle-fill"></i>
                                        </a>
                                        @endif
                                        <a href="javascript:void(0)"
                                           data-title="Are you sure you want to delete this seller?"
                                           data-text="This action cannot be undone!"
                                           data-confirm="Yes, delete it!"
                                           data-success="Deleted successfully!"
                                           data-href=" @encryptedRoute('sellerDelete',$staff->id)"
                                           class="btn btn-sm btn-danger sweet-confirm" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Delete">
                                           <i class="ri-delete-bin-2-line"></i>
                                        </a>

                                       </div>
                                      
                                        
                                    </div>
                                 </td>
                              </tr>
                              @endforeach
                              @endif
                           </tbody>
                        </table>
                        {{ $getStaffList1->links('pagination::bootstrap-5') }}
                    

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
@endsection