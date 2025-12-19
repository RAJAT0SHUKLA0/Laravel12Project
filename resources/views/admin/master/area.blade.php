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
                  $action = isset($area)
                     ? route('areaUpdate', $area->id)
                     : route('areaSave');
            @endphp
 <form action="{{$action}}" method="post">
                @csrf
                @if(isset($area))
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
                                                <label for="exampleFormControlTextarea5" class="form-label">STATE</label>
                                                <select id="" name="state_id" class="form-select" onchange="getCity(this.value,'{{ route('getCity') }}','{{ csrf_token() }}')" required>
                                                    <option selected="" value=''>SELECT</option>
                                                    @if(sizeof($getState))
                                                        @foreach($getState as $state)
                                                            <option value="{{ $state->id }}" {{ (isset($area->state_id) && $area->state_id == $state->id) ? 'selected' : '' }}>
                                                                {{ $state->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                             <div class="col-xxl-3 col-md-6">
                                        <div >
                                            <label for="" class="form-label">City</label>
                                            <select id=""  name="city_id" class="form-select appendcity"  required>
                                                <option selected="" value=''>SELECT</option>
                                                @if(isset($area) && sizeof($getAllCity))
                                                @foreach($getAllCity as $city)
                                                 <option value="{{isset($city->id)?$city->id:''}}" {{$city->id==$area->city_id?'selected':''}}>{{isset($city->name)?$city->name:''}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                           
                                         <div class="col-lg-4">
    <div>
        <label for="exampleFormControlTextarea5" class="form-label">AREA</label>
        <textarea class="form-control" name="name" id="exampleFormControlTextarea5" rows="3">{{ old('name', $area->name ?? '') }}</textarea>
    </div>
</div>

                                           
                                        </div>
                                    </div>
                                    <div>
                                                   <button type="submit" class="btn btn-primary">Submit</button>
                                                    
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
                            <h4 class="card-title mb-0">AREA LIST</h4>
                        </div><!-- end card header -->
            
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">
                                   
                                    <!--<div class="col-sm">-->
                                    <!--    <div class="d-flex justify-content-sm-end">-->
                                    <!--        <div class="search-box ms-2">-->
                                    <!--            <input type="text" class="form-control search" placeholder="Search...">-->
                                    <!--            <i class="ri-search-line search-icon"></i>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                </div>
            
                                <div class="table-responsive table-card mt-3 mb-1">
                                    <table class="table align-middle table-nowrap" id="customerTable">
                                        <thead class="table-light">
                                            <tr>
                                                        <th class="sort" data-sort="customer_no">SNO.</th>
                                                        <th class="sort" data-sort="customer_name">STATE</th>
                                                        <th class="sort" data-sort="email">CITY</th>
                                                        <th class="sort" data-sort="phone">AREA</th>
                                                        <th class="sort" data-sort="action">Action</th>
                                                    </tr>
                                        </thead>
                                         <tbody class="list form-check-all">
                                                         @if(sizeof($getAreaLists))
                                                @php
                                                 $i=1;
                                                @endphp
                                                @foreach($getAreaLists as $getArea)
                                                    <tr>
                                                
                                                        
                                                        <td class="customer_no">{{$i++}}</td>
                                                        
                                                        <td class="email">{{$getArea->getState->name}}</td>
                                                        <td class="email">{{$getArea->getCity->name}}</td>
                                                        <td class="customer_name">{{$getArea->name}}</td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <div class="edit">
                                                                    <a href="@encryptedRoute('editArea',$getArea->id)" class="btn btn-sm btn-success edit-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-pencil-line"></i></a>
                                                                </div>
                                                                <div class="remove">
                                                                    <a href = "@encryptedRoute('softDelete',$getArea->id)" class="btn btn-sm btn-danger remove-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-delete-bin-2-line"></i></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                        @endforeach
                                            @endif</tbody>
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