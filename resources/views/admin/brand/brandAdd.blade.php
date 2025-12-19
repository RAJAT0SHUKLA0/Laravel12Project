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
                                    $action =isset($getbrandInfo) ? route('brandupdate',$getbrandInfo->id):route('brandSave');
                                    @endphp
                                  
          
           <form action="{{$action}}" method="post" enctype="multipart/form-data">
                @csrf
               @if(isset($getbrandInfo))
                @method('PUT')
                @else
               @method('POST')
               @endif
               
               
               <div class="row">
                   <div class="col-lg-12">
                      <div class="card">
                         <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Brand Add</h4>
                         </div>
                         <!-- end card header -->
                         <div class="card-body">
                            <div class="live-preview">
                                <div class="row">
                                <div class="col-lg-4">
                                            <div>
                                                <label for="basiInput" class="form-label">BRAND NAME</label>
                                                <input type="text" name="name" value="{{!empty($getbrandInfo->name)?$getbrandInfo->name:''}}" class="form-control" id="basiInput" required>
                                            </div>
                                </div>
                                
                                <div class="col-md-4">
                              <label for="" class="form-label">image</label>
                              <input type="file" name="image" class="form-control" id="" >
                               @if(isset($getbrandInfo->image))
                                        <div>
                                          <img src="{{asset('storage/uploads/brand/'.$getbrandInfo->image)}}"  width="100px" alt="">
                                        </div>
                                        @endif
                           </div>
                                
                                        
                            <div class="col-lg-4 mt-4">
                                             <div class="col-sm-auto">
                                    <div>
                                        
                                        <button type="submit" class="btn btn-success add-btn" data-bs-toggle="" id="create-btn" data-bs-target=""><i class="ri-add-line align-bottom me-1"></i>      @if(!empty($getbrandInfo->name))
                                       Update
                                        @else
                                        Add
                                        @endif</button>
                                    </div>
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