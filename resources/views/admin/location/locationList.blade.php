@extends('admin.layout.layout')
@section('content')
<div class="page-content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">Location Filter</h4>
                  
               </div>
               <!-- end card header -->
               <div class="card-body">
                  <div class="live-preview">
                     <form  method="POST" action="{{ route('locationList') }}">
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
                  <h4 class="card-title mb-0">Location List</h4>
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
                                 <th class="" data-sort="role_id"> Latitude</th>
                                 <th class="" data-sort="staff_id">Longitude</th>
                                 <th class="" data-sort="role_id">Address</th>
                                 <th class="" data-sort="joining_date"> Date</th>
                                <th class="" data-sort="joining_date"> Action</th>
                              </tr>
                           </thead>
                           <tbody class="list form-check-all">
                              @if(sizeof($getAttendanceList))
                              @php
                              $i=1;
                              @endphp
                              @foreach($getAttendanceList as $AttendanceList)
                              <tr>
                                 <td class="id" style="">{{$i++}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->getUser->name)?$AttendanceList->getUser->name:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->latitude)?$AttendanceList->latitude:''}}</td>
                                 <td class="customer_name">{{isset($AttendanceList->longitude)?$AttendanceList->longitude:''}}</td>
                                @php
                                    $address = isset($AttendanceList->address) ? $AttendanceList->address : '';
                                    $wrappedAddress = wordwrap($address, 50, "\n", true);
                                    $lines = explode("\n", $wrappedAddress);
                                    $lines = array_slice($lines, 0, 6);
                                    while (count($lines) < 6) {
                                        $lines[] = '';
                                    }
                                @endphp
                                
                                <td class="customer_name" style="font-size: 12px;">
                                    {!! implode('<br>', $lines) !!}
                                </td>
                                 <td class="customer_name">{{isset($AttendanceList->date)?$AttendanceList->date:''}}<br>{{ $AttendanceList->created_at ? \Carbon\Carbon::parse($AttendanceList->created_at)->format('h:i A') : '' }}</td>
                                 <td class="customer_name"> <button  onclick="getrenderComponent('{{$AttendanceList->user_id}}','{{route('renderLocationList')}}','{{ csrf_token() }}')"  class="btn  btn-success " ><i class=" ri-checkbox-circle-fill"></i></button></td>
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
<div id="userCardContainer"></div>
@endsection