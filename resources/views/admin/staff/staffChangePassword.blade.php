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
           <form action="{{route('changePassword', request('id'))}}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
               <div class="row">
                   <div class="col-lg-12">
                      <div class="card">
                         <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Change Password</h4>
                         </div>
                         <!-- end card header -->
                         <div class="card-body">
                            <div class="live-preview">
                               <div class="row gy-4">
                                
                                    <div class="col-xxl-3 col-md-6">
                                         <div>
                                            <label for="basiInput" class="form-label">New Password</label>
                                            <input type="password" class="form-control"  name="password" id="basiInput" required>
                                         </div>
                                    </div>
                                     <div class="col-xxl-3 col-md-6">
                                         <div>
                                            <label for="basiInput" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control"  name="confirm_password" id="basiInput" required>
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