@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">Attendance Filter</h4>
                  
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="live-preview">
                     <form  method="POST" action="{{ route('attendanceList') }}">
                         @csrf
                         @method('POST')
                        <div class="row row-cols-lg-auto g-3 align-items-center">
                           <div class="col-12">
                               <select class="form-select" data-choices="" data-choices-sorting="true" name="name" id="state">
                                 <option value =''>Select User</option>
                                 @if(sizeof($getUsers))
                                     @foreach($getUsers as $user)
                                       <option value="{{isset($user->id)?$user->id:''}}" {{ isset($user->id) && old('status', request('name')) == $user->id ? 'selected' : '' }}>{{isset($user->name)?$user->name:''}}</option>
                                     @endforeach
                                 @endif
                              </select>
                           </div>
                           
                           <!--end col-->
                          
                           <!--end col-->
                         
                           <div class="col-12">
                              <div class="input-group">
                                 <input type="date" class="form-control" id="inlineFormInputGroupUsername" name="date"  placeholder=" Date" value="{{ request('date') }}">
                              </div>
                           </div>
                           <!--end col-->
                           <div class="col-12">
                              <button type="submit" class="btn btn-success">Submit</button>
                           </div>
                            <div class="col-12">
                              <a href="{{ route('attendanceList') }}" class="btn btn-primary">Reset</a>
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
                  <h4 class="card-title mb-0">Attendance List</h4>
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
                                 <th class="" data-sort="name">Staff Name</th>
                                 <th class="" data-sort="joining_date"> Date</th>
                                 <th class="" data-sort="email">In Time</th>
                                 <th class="" data-sort="mobile">Out TIme</th>
                                    <th class="" data-sort="role_id">In Time Latitude</th>
                                 <th class="" data-sort="staff_id">In Time Longitude</th>
                                    <th class="" data-sort="role_id">Out Time Latitude</th>
                                 <th class="" data-sort="staff_id">Out Time Longitude</th>
                                 <th class="" data-sort="role_id">In Time Location</th>
                                 <th class="" data-sort="staff_id">Out Time Location</th>
                              </tr>
                           </thead>
                           <tbody class="list form-check-all">
                              @if(sizeof($getAttendanceList))
                              @php
                              $i=1;
                              @endphp
                              @foreach($getAttendanceList as $AttendanceList)
                              @php
                                    $address = isset($AttendanceList->in_time_address) ? $AttendanceList->in_time_address : '';
                                    $wrappedAddress = wordwrap($address, 50, "\n", true);
                                    $lines = explode("\n", $wrappedAddress);
                                    $lines = array_slice($lines, 0, 2);
                                    while (count($lines) < 2) {
                                        $lines[] = '';
                                    }
                                    $address2 = isset($AttendanceList->out_time_address) ? $AttendanceList->out_time_address : '';
                                    $wrappedAddress = wordwrap($address2, 50, "\n", true);
                                    $lines2 = explode("\n", $wrappedAddress);
                                    $lines2 = array_slice($lines2, 0, 2);
                                    while (count($lines2) < 2) {
                                        $lines2[] = '';
                                    }
                                @endphp
                              <tr>
                                 <td class="id" >{{$i++}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->getUser->name)?$AttendanceList->getUser->name:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->date)?$AttendanceList->date:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->in_time)?$AttendanceList->in_time:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->out_time)?$AttendanceList->out_time:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->in_time_lat)?$AttendanceList->in_time_lat:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->in_time_long)?$AttendanceList->in_time_long:''}}</td>
                                  <td class="customer_name">{{isset($AttendanceList->out_time_lat)?$AttendanceList->out_time_lat:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->out_time_long)?$AttendanceList->out_time_long:''}}</td>
                                 <td class="customer_name">{!! implode('<br>', $lines) !!}</td>
                                 <td class="customer_name">{!! implode('<br>', $lines2) !!}</td>
                              </tr>
                              @endforeach
                              @endif
                           </tbody>
                        </table>
                      {{ $getAttendanceList->links('pagination::bootstrap-4') }}

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