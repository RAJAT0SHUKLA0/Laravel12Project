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
                  $action = isset($menu)
                     ? route('SubMenu', $menu->id)
                     : route('SubMenu');
            @endphp
          <form action="{{$action}}" method="post" enctype="multipart/form-data">
                @csrf
                @if(isset($menu))
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
                                                <div>
                                                    <label for="basiInput" class="form-label">Name</label>
                                                    <input type="text" name="name" value="{{!empty($menu->name)?$menu->name:''}}"class="form-control" id="basiInput" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">Image</label>
                                                    <input type="file" name="image" class="form-control" id="basiInput" required>
                                                    @if(isset($menu->image))
                                                        <div>
                                                             <img src="{{asset('storage/uploads/sub-menu/'.$menu->image)}}"  width="100px" alt="">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                           
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">Menu</label>
                                                    <select id=""  name="menu_id" class="form-select"  required>
                                                        <option selected="" value=''>Choose...</option>
                                                         @if(sizeof($getAllMenu))
                                                        @foreach($getAllMenu as $menus)
                                                         <option value="{{isset($menus->id)?$menus->id:''}}" {{isset($menu) && $menus->id==$menu->menu_id?'selected':''}}>{{isset($menus->name)?$menus->name:''}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">parent</label>
                                                    <select id=""  name="parent_id" class="form-select"  >
                                                        <option selected="" value=''>Choose...</option>
                                                         @if(sizeof($getAllparent))
                                                        @foreach($getAllparent as $parent)
                                                         <option value="{{isset($parent->id)?$parent->id:''}}" {{isset($menu) && $parent->id==$menu->parent_id?'selected':''}}>{{isset($parent->name)?$parent->name:''}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            
                                             <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">Type</label>
                                                    <select id=""  name="type" class="form-select"  required>
                                                        <option selected="" value=''>Choose...</option>
                                                         @if(sizeof($getAllType))
                                                        @foreach($getAllType as $types)
                                                         <option value="{{isset($types->id)?$types->id:''}}" {{isset($menu) && $types->id==$menu->type?'selected':''}}>{{isset($types->name)?$types->name:''}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">Color Code</label>
                                                    <input type="text" name="color_code" value="{{!empty($menu->color_code)?$menu->color_code:''}}"class="form-control" id="basiInput" required>
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
                            <h4 class="card-title mb-0">Menu List</h4>
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
                                                <th class="" data-sort="name">Image</th>
                                                                                                <th class="" data-sort="name">Link</th>

                                                <th class="" >Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @if(sizeof($getMenu))
                                                @php
                                                 $i=1;
                                                @endphp
                                                @foreach($getMenu as $menu)
                                                    <tr>
                                                        <td class="id" style="">{{$i++}}</td>
                                                        <td class="customer_name">{{isset($menu->name)?$menu->name:''}}</td>
                                                         <td class="customer_name">
                                                            @if(isset($menu->image))
                                                                <div>
                                                                     <img src="{{asset('storage/uploads/sub-menu/'.$menu->image)}}"  width="100px" alt="">
                                                                </div>
                                                            @endif
                                                         </td>
                                                                                                                <td class="customer_name">{{isset($menu->action)?$menu->action:''}}</td>

                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <div class="edit">
                                                                     <a href= "{{route('editSubMenu',[$menu->id])}}" class="btn btn-sm btn-success edit-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-pencil-line"></i></a>
                                                                </div>
                                                                <div class="remove">
                                                                    <a href="{{route('DeleteSubMenu',[$menu->id])}}" class="btn btn-sm btn-danger remove-item-btn" data-bs-toggle="" data-bs-target=""><i class="ri-delete-bin-2-line"></i></a>
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