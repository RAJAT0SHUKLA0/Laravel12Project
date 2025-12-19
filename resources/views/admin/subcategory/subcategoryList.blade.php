@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
     <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">SUBCATEGORY ADD</h4>
                  <div class="col-sm-auto">
                           <div>
                              <a href="{{route('subcategoryAdd')}}"  class="btn btn-success add-btn" ><i class="ri-add-line align-bottom me-1"></i> Add</a>
                           </div>
                        </div>
               </div>
               <!-- end card header -->
             <div class="card-body">
                  <div class="live-preview">
                     <form  method="POST" action="{{ route('subcategorylist') }}">
                         @csrf
                         @method('POST')
                        <div class="row row-cols-lg-auto g-3 align-items-center">
                            
                             <div class="col-12">
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="category_id" id="state" onchange="getCity(this.value,'{{route('getCity')}}','{{ csrf_token() }}')">
                                 <option value =''>Select Category</option>
                                 @if(sizeof($getState))
                                     @foreach($getState as $state)
                                       <option value="{{isset($state->id)?$state->id:''}}" {{ isset($state->id) && old('status', request('category_id')) == $state->id ? 'selected' : '' }}>{{isset($state->name)?$state->name:''}}</option>
                                     @endforeach
                                 @endif
                              </select>
                           </div>
                             
                           <div class="col-12">
                              <div class="input-group">
                                 <input type="text" name="name" class="form-control" id="inlineFormInputGroupUsername" placeholder="Subcategory Name" value="{{ request('name') }}">
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
                              <button type="submit" class="btn btn-success">Submit</button>
                           </div>
                            <div class="col-12">
                              <a href="{{ route('subcategorylist') }}" class="btn btn-primary">Reset</a>
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
                  <h4 class="card-title mb-0">SUBCATEGORY LIST</h4>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="" id="">
                      <div class="table-responsive table-card mt-3 mb-1">
                                    <table class="table align-middle table-nowrap" id="customerTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" >
                                                   S No
                                                </th>
                                                <th class="" data-sort="name">CATEGORY</th>
                                                <th class="" data-sort="name">SUBCATEGORY</th>
                                                <th class="" data-sort="name">DESCRIPTION</th>
                                                <th class="" >STATUS</th>
                                                <th class="" >Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @if(sizeof($getCityLists))
                                                @php
                                                 $i=1;
                                                @endphp
                                                @foreach($getCityLists as $getStateList)
                                                    <tr>
                                                        <td class="id" style="">{{$i++}}</td>
                                                        <td class="customer_name">{{$getStateList->getCategory->name??null}}</td>
                                                        <td class="customer_name">{{$getStateList->name}}</td>
                                                         <td class="customer_name">{{$getStateList->description?strip_tags($getStateList->description):''}}</td>
                                                          @if($getStateList->status ==1)
                                 <td class="customer_name"> <span class="badge bg-success">Active</span></td>
                                 @else
                                 <td class="customer_name"> <span class="badge bg-primary">In Active</span></td>
                                 @endif
                                                        <td>
                                    <div class="d-flex gap-2">
                                       <div class="edit">
                                          <!--@php-->
                                          <!--$id= \App\Utils\Crypto::encryptId($getStateList->id);-->
                                          <!--@endphp-->
                                          <a  href=" @encryptedRoute('editSubcategory',$getStateList->id)" class="btn  btn-sm btn-success"  data-bs-toggle="popover"
                                           data-bs-trigger="hover focus"
                                           data-bs-content="Edit Subcategory"><i class="ri-pencil-line"></i></a>
                                       </div>
                                       <div class="status">
                                        @if($getStateList->status == 1)
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the subcategory status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="@encryptedRoute('subcategorystatusUpdate',$getStateList->id,0)"
                                           class="btn btn-sm btn-success sweet-confirm"
                                           data-bs-toggle="popover"
                                            data-bs-trigger="hover focus"
                                            data-bs-content="Deactivate subcategory">
                                           <i class="ri-checkbox-circle-fill"></i>
                                        </a>
                                        @else
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the subcategory status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="@encryptedRoute('subcategorystatusUpdate',$getStateList->id, 1)"
                                           class="btn btn-sm btn-primary sweet-confirm"
                                           data-bs-toggle="popover"
                                            data-bs-trigger="hover focus"
                                            data-bs-content="Activate subcategory">
                                           <i class="ri-indeterminate-circle-fill"></i>
                                        </a>
                                        @endif
                                        <a href="javascript:void(0)"
                                           data-title="Are you sure you want to delete this subcategory?"
                                           data-text="This action cannot be undone!"
                                           data-confirm="Yes, delete it!"
                                           data-success="Deleted successfully!"
                                           data-href="@encryptedRoute('softDeleteSubcategory',$getStateList->id)"
                                           class="btn btn-sm btn-danger sweet-confirm" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Delete subcategory">
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