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
     
      <form action="{{route('orderAssignSave')}}" method="post" enctype="multipart/form-data">
         @csrf
         @method('POST')
         <div class="row">
            <div class="col-lg-12">
               <div class="card">
                  <div class="card-header align-items-center d-flex">
                     <h4 class="card-title mb-0 flex-grow-1">Order Assign</h4>
                  </div>
                  <!-- end card header -->
                 
                      <div class="card-body">
                         <div class="live-preview">
                            <div class="row gy-4">
                               <div class="col-xxl-3 col-md-6">
                                  <div >
                                     <label for="" class="form-label">Beat</label>
                                     <select id=""  name="beat_id[]" class="form-select beat"  onchange="renderOrderList('{{route('orderAssign')}}','{{ csrf_token() }}',this)" multiple required>
                                        <option selected="" value=''>Choose...</option>
                                        @if(sizeof($area))
                                            @foreach($area as $beat)
                                                <option value="{{isset($beat->id)?$beat->id:''}}" >{{isset($beat->name)?$beat->name:''}}</option>
                                            @endforeach
                                        @endif
                                     </select>
                                  </div>
                               </div>
                               <div class="col-xxl-3 col-md-6">
                                  <div >
                                     <label for="" class="form-label">Rider</label>
                                     <select id=""  name="rider_id" class="form-select"  required>
                                        <option selected="" value=''>Choose...</option>
                                        @if(sizeof($rider))
                                            @foreach($rider as $riderData)
                                                <option value="{{isset($riderData->id)?$riderData->id:''}}"  >{{isset($riderData->name)?$riderData->name:''}}</option>
                                            @endforeach
                                        @endif
                                     </select>
                                  </div>
                               </div>
                               <div id="orderListAppend"></div>
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