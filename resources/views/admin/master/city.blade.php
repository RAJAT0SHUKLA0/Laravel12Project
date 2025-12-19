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
                    <!-- end page title -->
                    @php
                  $action = isset($city)
                     ? route('cityUpdate', $city->id)
                     : route('citySave');
            @endphp
 <form action="{{$action}}" method="post">
                @csrf
                @if(isset($city))
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
                                                 <label for="basiInput" class="form-label">State</label>
                                                <select class="form-select mb-3" name="state_id" aria-label="Default select example" onchange="getCity(this.value,'{{ route('getCity') }}','{{ csrf_token() }}')" required>
                                                    <option selected="">Select</option>
                                                    
                                                   @if(sizeof($getState))
                                                        @foreach($getState as $state)
                                                            <option value="{{ $state->id }}" {{ (isset($city->state_id) && $city->state_id == $state->id) ? 'selected' : '' }}>
                                                                {{ $state->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                           

                                           
                                           <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">City</label>
                                                    <input type="text" name="name" class="form-control"  value="{{isset($city->name)?$city->name:''}}" id="basiInput" required>
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
                            <h4 class="card-title mb-0">City List</h4>
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
                                                <th class="" data-sort="name">State</th>
                                                <th class="" data-sort="name">City</th>
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
                                                        <td class="customer_name">{{$getStateList->getState->name??null}}</td>
                                                        <td class="customer_name">{{$getStateList->name}}</td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <div class="edit">
                                                                   <a href= "@encryptedRoute('editCity',$getStateList->id)" class="btn btn-sm btn-success edit-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-pencil-line"></i></a>
                                                                </div>
                                                                <div class="remove">
                                                                    <a href="@encryptedRoute('softDeleteCity',$getStateList->id)" class="btn btn-sm btn-danger remove-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-delete-bin-2-line"></i></a>
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