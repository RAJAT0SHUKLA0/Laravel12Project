@extends('admin.layout.layout')
@section('content')

    <div class="page-content">
        <div class="container-fluid">
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
                  $action = isset($getStaffInfo)
                     ? route('sellerupdate', $getStaffInfo->id)
                     : route('sellerSave');
            @endphp
           <form action="{{$action}}" method="post" enctype="multipart/form-data">
               @csrf
                @if(isset($getStaffInfo))
                @method('PUT')
                @else
                @method('POST')
                @endif
               
               <div class="row">
                   <div class="col-lg-12">
                      <div class="card">
                         <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Seller Add</h4>
                         </div>
                         <!-- end card header -->
                         <div class="card-body">
                            <div class="live-preview">
                               <div class="row gy-4">
                                   
                                  <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Name</label>
                                        <input type="text" class="form-control"  name="name"id="basiInput"  value="{{!empty($getStaffInfo->name)?$getStaffInfo->name:''}}"required>
                                     </div>
                                  </div>
                                   <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">ShopName</label>
                                        <input type="text" class="form-control"  name="shop_name"id="basiInput"  value="{{!empty($getStaffInfo->shop_name)?$getStaffInfo->shop_name:''}}"required>
                                     </div>
                                  </div>
                                  <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">email</label>
                                        <input type="email" class="form-control"  name="email"id="basiInput" value="{{!empty($getStaffInfo->email)?$getStaffInfo->email:''}}" required>
                                     </div>
                                  </div>
                                  <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Mobile</label>
                                        <input type="phone" class="form-control"  name="mobile"id="basiInput" value="{{!empty($getStaffInfo->mobile)?$getStaffInfo->mobile:''}}" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                     </div>
                                  </div>
                                  <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Whatsapp No.</label>
                                        <input type="phone" class="form-control"  name="whatsapp_no"id="basiInput" value="{{!empty($getStaffInfo->whatsapp_no)?$getStaffInfo->whatsapp_no:''}}" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                     </div>
                                  </div>
                                  
                                  
                                 
                                   <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Profile Pic</label>
                                        <input type="file" class="form-control"  name="profile_pic"id="basiInput"  {{!empty($getStaffInfo->profile_pic)?'':''}} >
                                        @if(isset($getStaffInfo->profile_pic))
                                        <div>
                                          <img src="{{asset('storage/uploads/profile/'.$getStaffInfo->profile_pic)}}"  width="100px" alt="">
                                        </div>
                                        @endif
                                     </div>
                                  </div>
                                  <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Aadhar front pic</label>
                                        <input type="file" class="form-control" name="addhar_front_pic"id="basiInput" {{!empty($getStaffInfo->addhar_front_pic)?'':'required'}}>
                                        @if(isset($getStaffInfo->addhar_front_pic))
                                        <div>
                                          <img src="{{asset('storage/uploads/aadhar/'.$getStaffInfo->addhar_front_pic)}}" width="100px" alt="">
                                        </div>
                                        @endif
                                     </div>
                                  </div>
                                    <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Aadhar back pic</label>
                                        <input type="file" class="form-control"  name="addhar_back_pic"id="basiInput" {{!empty($getStaffInfo->addhar_back_pic)?'':'required'}}>
                                        @if(isset($getStaffInfo->addhar_back_pic))
                                        <div>
                                          <img src="{{asset('storage/uploads/aadhar/'.$getStaffInfo->addhar_back_pic)}}" width="100px" alt="">
                                        </div>
                                        @endif

                                     </div>
                                    </div>
                                     <div class="col-xxl-3 col-md-6">
                                   <label for="" class="form-label">Seller Type</label>
                                                <select  name="sellertype_id" class="form-select" aria-label=".form-select-sm example">
                                                    <option selected="">select</option>
                                                    <option value="1">Retailer</option>
                                                    <option value="2">wholeSeler</option>
                                                    
                                                </select>
                                    </div>
                                    
                                      <div class="col-xxl-3 col-md-6">
                                        <div >
                                            <label for="" class="form-label">State</label>
                                            <select id="" name="state_id" class="form-select" onchange="getCity(this.value,'{{route('getCity')}}','{{ csrf_token() }}')" required>
                                                <option selected="" value=''>Choose...</option>
                                                @if(sizeof($getState))
                                                @foreach($getState as $state)
                                                 <option value="{{isset($state->id)?$state->id:''}}" {{isset($getStaffInfo) && $state->id==$getStaffInfo->state_id?'selected':''}}>{{isset($state->name)?$state->name:''}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-md-6">
                                                <label for="" class="form-label">City</label>
                                                <select name="city_id" class="form-select appendcity"
                                                onchange="getArea(this.value,'{{ route('getArea') }}', '{{ csrf_token() }}')" required>
                                                <option selected="" value=''>Choose...</option>
                                                @if(isset($getStaffInfo) && sizeof($getAllCity))
                                                @foreach($getAllCity as $city)
                                                <option value="{{isset($city->id)?$city->id:''}}" {{$city->id==$getStaffInfo->city_id?'selected':''}}>{{isset($city->name)?$city->name:''}}</option>
                                                @endforeach
                                                @endif
                                                </select>
                                    </div>
                                        <div class="col-xxl-3 col-md-6">
                                            <label for="" class="form-label">Beat</label>
                                            <select name="beat_id" class="form-select appendarea">
                                                <option selected="" value=''>choose...</option>
                                                 @if(isset($getStaffInfo) && sizeof($getArea))
                                                @foreach($getArea as $area)
                                                <option value="{{isset($area->id)?$area->id:''}}" {{$area->id==$getStaffInfo->beat_id?'selected':''}}>{{isset($area->name)?$area->name:''}}</option>
                                                @endforeach
                                                @endif
                                            </select>

                                        </div>
                                 <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Latitude</label>
                                        <input type="text" class="form-control"  name="latitude"id="basiInput"  value="{{!empty($getStaffInfo->latitude)?$getStaffInfo->latitude:''}}"required>
                                     </div>
                                  </div>
                                   <div class="col-xxl-3 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Longitude</label>
                                        <input type="text" class="form-control"  name="longitude"id="basiInput"  value="{{!empty($getStaffInfo->longitude)?$getStaffInfo->longitude:''}}"required>
                                     </div>
                                  </div>

                                     <div class="col-xxl-6 col-md-12">
                                    <div>
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" name="address" id="address" rows="1" required>{{ !empty($getStaffInfo->address) ? $getStaffInfo->address : '' }}</textarea>
                                    </div>

                                  </div>
                                 
                                    
                                  <!--end col-->
                               </div>
                               <div class="col-lg-12 mt-4">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                               <!--end row-->
                            </div>
                         </div>
                      </div>
                   </div>
                   <!--end col-->
                </div>
           </form>
        </div>
    </div>
@endsection