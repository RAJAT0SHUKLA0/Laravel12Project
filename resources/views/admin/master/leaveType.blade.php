@extends('admin.layout.layout')
@section('content')

<div class="container-fluid mt-5">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0"></h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                                        <li class="breadcrumb-item active"></li>
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
                    <!-- end page title -->
                      @php
                  $action = isset($getLeave)
                     ? route('leaveTypeUpdate', $getLeave->id)
                     : route('leaveTypeSave');
            @endphp
            
 <form action="{{($action)}}" method="post">
                @csrf
               @if(isset($getLeave))
               @method ('PUT')
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
                                                <div>
                                                    <label for="basiInput" class="form-label">LEAVE TYPE</label>
                                                    <input type="text" name="name" value="{{!empty($getLeave->name)?$getLeave->name:''}}"class="form-control" id="basiInput">
                                                </div>
                                            </div>
                                            
                                             <div class="col-lg-4 mt-4">
                                                 <div class="col-sm-auto">
                                        <div>
                                            <button type="submit" class="btn btn-success add-btn" data-bs-toggle="" id="create-btn" data-bs-target=""><i class="ri-add-line align-bottom me-1"></i> Add</button>
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
                            <h4 class="card-title mb-0">LeaveType List</h4>
                        </div><!-- end card header -->
            
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">
                                   
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
                                                <th class="" data-sort="name">Name</th>
                                                
                                                <th class="" >Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @if(sizeof($getLeaveLists))
                                            
                                                @php
                                                 $i=1;
                                                @endphp
                                                @foreach($getLeaveLists as $getLeaveList)
                                                
                                                    <tr>
                                                        <td class="id" style="">{{$i++}}</td>
                                                        <td class="customer_name">{{isset($getLeaveList->name)?$getLeaveList->name:''}}</td>
                                                        <!--<td class="customer_name"> <span class="badge bg-success">Active</span></td>-->
                                                        <!-- <td class="customer_name"> <span class="badge bg-primary">In Active</span></td>-->
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <div class="edit">
                                                                     <a href= "@encryptedRoute('editLeaveType',$getLeaveList->id)" class="btn btn-sm btn-success edit-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-pencil-line"></i></a>
                                                                </div>
                                                                <div class="remove">
                                                                    <a href="@encryptedRoute('softDeleteLeaveType',$getLeaveList->id)" class="btn btn-sm btn-danger remove-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-delete-bin-2-line"></i></a>
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