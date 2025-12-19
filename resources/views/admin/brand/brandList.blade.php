@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
     <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">BRAND ADD</h4>
                  <div class="col-sm-auto">
                           <div>
                              <a href="{{route('brandAdd')}}"  class="btn btn-success add-btn" ><i class="ri-add-line align-bottom me-1"></i> ADD</a>
                           </div>
                        </div>
               </div>
               <!-- end card header -->
             
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
                  <h4 class="card-title mb-0">BRAND LIST</h4>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="" id="">
                     <div class="table-responsive table-card mt-3 mb-1">
                        <table class="table align-middle table-nowrap" id="">
                           <thead class="table-light">
                              <tr>
                                 <th scope="col" >
                                    S No
                                 </th>
                                 <th class="" data-sort="name">Image</th>
                                 <th class="" data-sort="name">Brand</th>
                                 <th class="" data-sort="name">status</th>
                                 
                                 <th class="" >Action</th>
                              </tr>
                           </thead>
                           <tbody class="list form-check-all">
                              @if(sizeof($getBrandList))
                              @php
                              $i=1;
                              @endphp
                              @foreach($getBrandList as $getCategory)
                              <tr>
                                 <td class="id" >{{ $getBrandList->firstItem() + $loop->index }}</td>
                                 <td class="customer_name">  
                                 @if(isset($getCategory->image))
                                    
                                    <img src="{{asset('storage/uploads/brand/'.$getCategory->image)}}"  width="50px" alt="">
                                 
                                  @endif
                                        </td>
                                 <td class="customer_name">{{isset($getCategory->name)?$getCategory->name:''}}</td>
                               
                                  
                                 @if($getCategory->status ==1)
                                 <td class="customer_name"> <span class="badge bg-success">Active</span></td>
                                 @else
                                 <td class="customer_name"> <span class="badge bg-primary">In Active</span></td>
                                 @endif
                                 
                                 <td>
                                    <div class="d-flex gap-2">
                                       <div class="edit">
                                         
                                          <a  href="@encryptedRoute ('brandEdit',$getCategory->id)" class="btn  btn-sm btn-success"><i class="ri-pencil-line"  data-bs-toggle="popover"
                                           data-bs-trigger="hover focus"
                                           data-bs-content="Edit Brand"></i></a>
                                       </div>
                                       <div class="status">
                                        @if($getCategory->status == 1)
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the brand status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="@encryptedRoute('brandstatusUpdate',$getCategory->id,0)"
                                           class="btn btn-sm btn-success sweet-confirm"
                                           
                                           data-bs-toggle="popover"
                                            data-bs-trigger="hover focus"
                                            data-bs-content="Deactivate brand">
                                          <i class="ri-checkbox-circle-fill"></i>
                                        </a>
                                        @else
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the brand status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="@encryptedRoute('brandstatusUpdate',$getCategory->id,1)"
                                           class="btn btn-sm btn-primary sweet-confirm"
                                            data-bs-toggle="popover"
                                               data-bs-trigger="hover focus"
                                               data-bs-content="Activate brand">
                                            
                                           <i class="ri-indeterminate-circle-fill"></i>
                                        </a>
                                        @endif
                                        <a href="javascript:void(0)"
                                           data-title="Are you sure you want to delete this brand?"
                                           data-text="This action cannot be undone!"
                                           data-confirm="Yes, delete it!"
                                           data-success="Deleted successfully!"
                                           data-href="@encryptedRoute('brandDelete',$getCategory->id)"
                                           class="btn btn-sm btn-danger sweet-confirm" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Delete Brand">
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
                      {{ $getBrandList->links('pagination::bootstrap-5') }}

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