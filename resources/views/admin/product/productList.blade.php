@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">Product Filter</h4>
                  <div class="col-sm-auto">
                           <div>
                              <a href="{{route('ProductAdd')}}"  class="btn btn-success add-btn" ><i class="ri-add-line align-bottom me-1"></i> Add Product</a>
                           </div>
                        </div>
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="live-preview">
                     <form  method="POST" action="{{ route('Product') }}">
                         @csrf
                         @method('POST')
                        <div class="row row-cols-lg-auto g-3 align-items-center">
                           <div class="col-12">
                              <div class="input-group">
                                 <input type="text" name="name" class="form-control" id="inlineFormInputGroupUsername" placeholder="Name" value="{{ request('name') }}">
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
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="category_id" id="state" onchange="getSubCategory(this.value,'{{route('getSubcategory')}}','{{ csrf_token() }}')">
                                 <option value =''>Select Category</option>
                                  @if(sizeof($getCategory))
                                 @foreach($getCategory as $category)
                                 <option value="{{isset($category->id)?$category->id:''}}" {{ isset($category->id) && old('status', request('category_id')) == $category->id ? 'selected' : '' }}>{{isset($category->name)?$category->name:''}}</option>
                                 @endforeach
                                 @endif
                              </select>
                           </div>
                           <div class="col-12">
                              <select class="form-select appendsub" data-choices="" data-choices-sorting="true" name="brand_id" id="city">
                                 <option value =''>Select Brand</option>
                               @if( sizeof($brands))
                                     @foreach($brands as $brand_data)
                                       <option value="{{isset($brand_data->id)?$brand_data->id:''}}" {{ isset($brand_data->id) && old('brand_id', request('brand_id')) == $brand_data->id ? 'selected' : '' }}>{{isset($brand_data->name)?$brand_data->name:''}}</option>
                                     @endforeach
                                 @endif
                              </select>
                           </div>
                            <div class="col-12">
                              <select class="form-select" data-choices="" data-choices-sorting="true" name="varient_id" id="role">
                                 <option value =''>Select Varient</option>
                                 @if(sizeof($getVarient))
                                 @foreach($getVarient as $varient)
                                 <option value="{{isset($varient->id)?$varient->id:''}}" {{ isset($varient->id) && old('status', request('varient_id')) == $varient->id ? 'selected' : '' }}>{{isset($varient->name)?$varient->name.' '.$varient->unit->name:''}}</option>
                                 @endforeach
                                 @endif
                              </select>
                           </div>
                           <!--end col-->
                           <div class="col-12">
                              <button type="submit" class="btn btn-success">Submit</button>
                           </div>
                            <div class="col-12">
                              <a href="{{ route('Product') }}" class="btn btn-primary">Reset</a>
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
                  <h4 class="card-title mb-0">Product List</h4>
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
                                 <th class="" data-sort="name">Name</th>
                                 <th class="" data-sort="email">category</th>
                                 <th class="" data-sort="mobile">Brand</th>
                                 <th class="" data-sort="role_id">varient</th>
                                 <th class="" data-sort="name">status</th>
                                 <th class="" >Action</th>
                              </tr>
                           </thead>
                           <tbody class="list form-check-all">
                              @if(sizeof($getProductsList))
                              @php
                              $i=1;
                              @endphp
                              @foreach($getProductsList as $product)
                              <tr>
                                 <td class="id" >{{ $getProductsList->firstItem() + $loop->index }}</td>
                                 <td class="customer_name">{{isset($product->name)?$product->name:''}}</td>
                                 <td class="customer_name">{{isset($product->category->name)?$product->category->name:''}}</td>
                                 <td class="customer_name">{{isset($product->brand->name)?$product->brand->name:'N/A'}} </td>
                                 <td class="customer_name">
                                   @php
                                    $details = $product->details;
                                @endphp
                                
                                @forelse($details->take(3) as $detail)
                                    <span class="badge bg-success">
                                        {{ $detail->varient->name ?? '' }} {{ $detail->varient->unit->name ?? '' }}
                                    </span>
                                @empty
                                    N/A
                                @endforelse
                                
                                @if($details->count() > 3)
                                    ...
                                @endif
                                
                                </td>

                                 @if($product->status ==1)
                                 <td class="customer_name"> <span class="badge bg-success">Active</span></td>
                                 @else
                                 <td class="customer_name"> <span class="badge bg-primary">In Active</span></td>
                                 @endif
                                 
                                 <td>
                                    <div class="d-flex gap-2">
                                       <div class="edit">
                                          
                                          <a  href="@encryptedRoute('ProductEdit',$product->id)" class="btn  btn-sm btn-success"  data-bs-toggle="popover"
                                           data-bs-trigger="hover focus"
                                           data-bs-content="Edit Product"><i class="ri-pencil-line"></i></a>
                                       </div>
                                       <div class="status">
                                         @if($product->status == 1)
                                            <a href="javascript:void(0)"
                                               data-title="Change Status?"
                                               data-text="Do you want to change the user status?"
                                               data-confirm="Yes, change it!"
                                               data-success="Status updated successfully!"
                                               data-href="@encryptedRoute('ProductstatusUpdate',$product->id,0)"
                                               class="btn btn-sm btn-success sweet-confirm"
                                               data-bs-toggle="popover"
                                               data-bs-trigger="hover focus"
                                               data-bs-content="Deactivate Product">
                                               <i class="ri-checkbox-circle-fill"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                               data-title="Change Status?"
                                               data-text="Do you want to change the user status?"
                                               data-confirm="Yes, change it!"
                                               data-success="Status updated successfully!"
                                               data-href="@encryptedRoute('ProductstatusUpdate',$product->id,1)"
                                               class="btn btn-sm btn-primary sweet-confirm"
                                               data-bs-toggle="popover"
                                               data-bs-trigger="hover focus"
                                               data-bs-content="Activate Product">
                                               <i class="ri-indeterminate-circle-fill"></i>
                                            </a>
                                        @endif
                                        
                                        <a href="javascript:void(0)"
                                           data-title="Are you sure you want to delete this staff?"
                                           data-text="This action cannot be undone!"
                                           data-confirm="Yes, delete it!"
                                           data-success="Deleted successfully!"
                                           data-href="@encryptedRoute('ProductstatusUpdate',$product->id,3)"
                                           class="btn btn-sm btn-danger sweet-confirm"
                                           data-bs-toggle="popover"
                                           data-bs-trigger="hover focus"
                                           data-bs-content="Delete Product">
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
                      {{ $getProductsList->links('pagination::bootstrap-5') }}

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