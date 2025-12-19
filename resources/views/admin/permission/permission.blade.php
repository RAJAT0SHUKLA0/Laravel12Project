@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
      <div class="row">
    <!-- Role Selection -->
    @php
                  $action = isset($getPermmissionInfo)
                     ? route('Permission', $getPermmissionInfo->id)
                     : route('Permission');
            @endphp
<form method="post" action="{{$action}}">
    @csrf
                @if(isset($getPermmissionInfo))
                @method('PUT')
                @else
                @method('POST')
                @endif
    
    <!-- Permissions Table -->
    <div class="card">
      <div class="card-header">
        <h5>Permissions  <span id="selectedRole"></span></h5>
      </div>
      <div class="card-body">
          
           <div class="mb-4">
      <label for="roleSelect" class="form-label">Select Role</label>
      <select id="roleSelect" class="form-select" name="role_id" required>
        <option value="">-- Choose Role --</option>
        @if(sizeof($getrole))
        @foreach($getrole as $role)
          <option value="{{isset($role->id)?$role->id:''}}" {{isset($getPermmissionInfo) && $getPermmissionInfo->role_id ==$role->id?'selected':''}}>{{isset($role->name)?$role->name:''}}</option>
        @endforeach
        @endif
        
      </select>
    </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 100%;">Menu</th>
               
              </tr>
            </thead>
            <tbody>
              <!-- Loop for each module -->
              <tr>
                <td>
                @if(sizeof($getmenu))
                @foreach($getmenu as $menu)
                          <div class="form-check">
                              @php
                              $selected ='';
                              $menuarray = isset($getPermmissionInfo->menu_id)&& !empty($getPermmissionInfo->menu_id)?explode(',',$getPermmissionInfo->menu_id ):'';
                              @endphp
                              @if(isset($getPermmissionInfo->menu_id) && in_array((string)$menu->id, $menuarray))
                              @php
                               $selected ='checked';
                               @endphp
                              @else
                              @php
                              $selected ='';
                              @endphp
                              @endif
                            <input class="form-check-input" type="checkbox" id="mod_dashboard" name="menu_id[]" value="{{isset($menu->id)?$menu->id:''}}" {{ $selected}}>
                            <label class="form-check-label" for="mod_dashboard">{{isset($menu->name)?$menu->name:''}}</label>
                          </div>
                @endforeach
                @endif
                </td>
              </tr>

              
              <!-- Add more modules similarly -->
            </tbody>
          </table>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 100%;">Sub Menu</th>
               
              </tr>
            </thead>
            <tbody>
              <!-- Loop for each module -->
              <tr>
                <td>
                @if(sizeof($getsub_menu))
                @foreach($getsub_menu as $smenu)
                          <div class="form-check">
                              @php
                              $selected ='';
                              $smenuarray = isset($getPermmissionInfo->sub_menu)&& !empty($getPermmissionInfo->sub_menu)?explode(',',$getPermmissionInfo->sub_menu ):'';
                              @endphp
                              @if(isset($getPermmissionInfo->sub_menu) && in_array((string)$smenu->id, $smenuarray))
                              @php
                               $selected ='checked';
                               @endphp
                              @else
                              @php
                              $selected ='';
                              @endphp
                              @endif
                            <input class="form-check-input" type="checkbox" id="mod_dashboard" name="sub_menu_id[]" value="{{isset($smenu->id)?$smenu->id:''}}" {{ $selected }}>
                            <label class="form-check-label" for="mod_dashboard">{{isset($smenu->name)?$smenu->name:''}}</label>
                          </div>
                @endforeach
                @endif
                </td>
              </tr>

              
              <!-- Add more modules similarly -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer text-end">
        <button class="btn btn-primary">Save Permissions</button>
      </div>
    </div>
    </form>

  </div>
    </div>
  </div>
  
  
      <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Sub Menu Type List</h4>
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
                                                <th class="" data-sort="name">role</th>
                                                <th class="" data-sort="name">Menu</th>
                                                <th class="" data-sort="name">Sub Menu</th>
                                                <th class="" >Action</th>
                                            </tr>
                                        </thead>
                                       <tbody class="list form-check-all">
    @if(sizeof($getPermmission))
        @php
            $i = 1;
        @endphp
        @foreach($getPermmission as $Permmission)
            <tr>
                <td class="id">{{ $i++ }}</td>
                <td class="customer_name">{{ $Permmission->role->name ?? '' }}</td>
                @php
                    // Extract names from collection and convert to array
                    $menuNames = $Permmission->menus->pluck('name')->toArray();
                    $submenuNames = $Permmission->submenus->pluck('name')->toArray();
                @endphp
                <td class="customer_name">{{ implode(',', $menuNames) }}</td>
                <td class="customer_name">{{ implode(',', $submenuNames) }}</td>
                <td>
                    <div class="d-flex gap-2">
                        <div class="edit">
                            <a href="{{ route('edit', [$Permmission->id]) }}" class="btn btn-sm btn-success edit-item-btn">
                                <i class="ri-pencil-line"></i>
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