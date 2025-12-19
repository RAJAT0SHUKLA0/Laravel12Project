@extends('admin.layout.layout')
@section('content')

 <div class="container-fluid mt-5">

                    <!-- start page title -->
                   <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0">Form Select</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                                        <li class="breadcrumb-item active">Form Select</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                     @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
             
            
            
              @php
                  $action = isset($varient)
                     ? route('VarientUpdate', $varient->id)
                     : route('varientSave');
            @endphp
 <form action="{{$action}}" method="post">
                @csrf
                @if(isset($varient))
                @method('PUT')
                @else
                @method('POST')
                @endif
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                               
                                <div class="card-body">
                                    
                                    <div class="live-preview">
                                        <div class="row">
                                             <div class="col-lg-4">
                                                 <label for="basiInput" class="form-label">UNIT</label>
                                                <select class="form-select mb-3" name="unit_id" aria-label="Default select example" required>
                                                    <option selected="">Select</option>
                                                    
                                                   @if(sizeof($getUnit))
                                                  
                                                        @foreach($getUnit as $unit)
                                                            <option value="{{ $unit->id }}" {{ (isset($varient->unit_id) && $varient->unit_id == $unit->id) ? 'selected' : '' }}>
                                                                {{ $unit->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                           

                                           
                                           <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">VARIENT</label>
                                                    <input type="text" name="name" class="form-control"  value="{{isset($varient->name)?$varient->name:''}}" id="basiInput" required>
                                                </div>
                                            </div>
                                            
                                             <div class="col-lg-4 mt-4">
                                                 <div class="col-sm-auto">
                                        <div>
                                            @if(isset($varient->id))
                                            <button type="submit" class="btn btn-success add-btn" data-bs-toggle="" id="create-btn" data-bs-target=""><i class="ri-add-line align-bottom me-1"></i> UPDATE</button>
                                            @else
                                            <button type="submit" class="btn btn-success add-btn" data-bs-toggle="" id="create-btn" data-bs-target=""><i class="ri-add-line align-bottom me-1"></i> ADD</button>
                                            @endif
                                        </div>
                                    </div>
                                            </div>


                                           
                                        </div>
                                        
                                        
                                    </div>
                                    
                                    
                                </div>
                                
                            </div>
                            
                            
                        </div> <!-- end col -->
                    </div>
                    </form>
</div>

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">VARIENT LIST</h4>
                        </div><!-- end card header -->
            
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">
                                    <!--<div class="col-sm-auto">-->
                                    <!--    <div>-->
                                    <!--        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Add</button>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="col-sm">
                                        <div class="d-flex justify-content-sm-end">
                                            <div class="search-box ms-2">
                                                <input type="text" class="form-control search" placeholder="Search...">
                                                <i class="ri-search-line search-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                <div class="table-responsive table-card mt-3 mb-1">
                                    <table class="table align-middle table-nowrap" id="customerTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" >
                                                   S No
                                                </th>
                                                <th class="" data-sort="name">UNIT</th>
                                                <th class="" data-sort="name">VARIENT NAME</th>
                                                <th class="" >STATUS</th>
                                                <th class="" >Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @if(sizeof($getVarientList))
                                                @php
                                                 $i=1;
                                                @endphp
                                                @foreach($getVarientList as $getVarient)
                                                    <tr>
                                                        <td class="id" style="">{{$i++}}</td>
                                                        <td class="customer_name">{{$getVarient->unit->name??null}}</td>
                                                        <td class="customer_name">{{$getVarient->name}}</td>
                                                         
                                                          @if($getVarient->status ==1)
                                 <td class="customer_name"> <span class="badge bg-success">Active</span></td>
                                 @else
                                 <td class="customer_name"> <span class="badge bg-primary">In Active</span></td>
                                 @endif
                                                        <td>
                                    <div class="d-flex gap-2">
                                       <div class="edit">
                                          <!--@php-->
                                          <!--$id= \App\Utils\Crypto::encryptId($getVarient->id);-->
                                          <!--@endphp-->
                                          <a  href="@encryptedRoute('editvarient',$getVarient->id)" class="btn  btn-sm btn-success"><i class="ri-pencil-line"></i></a>
                                       </div>
                                       <div class="status">
                                        @if($getVarient->status == 1)
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the varient status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="@encryptedRoute('varientstatusUpdate',$getVarient->id, 0)"
                                           class="btn btn-sm btn-success sweet-confirm">
                                           <i class="ri-checkbox-circle-fill"></i>
                                        </a>
                                        @else
                                        <a href="javascript:void(0)"
                                           data-title="Change Status?"
                                           data-text="Do you want to change the varient status?"
                                           data-confirm="Yes, change it!"
                                           data-success="Status updated successfully!"
                                           data-href="@encryptedRoute('varientstatusUpdate',$getVarient->id, 1)"
                                           class="btn btn-sm btn-primary sweet-confirm">
                                           <i class="ri-indeterminate-circle-fill"></i>
                                        </a>
                                        @endif
                                        <a href="javascript:void(0)"
                                           data-title="Are you sure you want to delete this varient?"
                                           data-text="This action cannot be undone!"
                                           data-confirm="Yes, delete it!"
                                           data-success="Deleted successfully!"
                                           data-href="@encryptedRoute('softDeleteVarient', $getVarient->id)"
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
                                    <div class="noresult" style="display: none">
                                        <div class="text-center">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                            <h5 class="mt-2">Sorry! No Result Found</h5>
                                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders for you search.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end col -->
            </div>
        </div>
    </div>
@endsection