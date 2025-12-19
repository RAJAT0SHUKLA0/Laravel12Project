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
                                    $action =isset($getcategoryInfo) ? route('categoryUpdate',$getcategoryInfo->id):route('categorySave');
                                    @endphp
          
           <form action="{{$action}}" method="post" enctype="multipart/form-data">
                @csrf
                @if(isset($getcategoryInfo))
                @method('PUT')
                @else
               @method('POST')
               @endif
               
               <div class="row">
                   <div class="col-lg-12">
                      <div class="card">
                         <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Category Add</h4>
                         </div>
                         <!-- end card header -->
                         <div class="card-body">
                            <div class="live-preview">
                               <div class="row gy-4">
                                  <div class="col-xxl-6 col-md-6">
                                     <div>
                                        <label for="basiInput" class="form-label">Category Name</label>
                                        <input type="text" class="form-control"  name="name" id="basiInput"  value="{{!empty($getcategoryInfo->name)?$getcategoryInfo->name:''}}"required>
                                     </div>
                                  </div>
                                  <div class="col-xxl-6 col-md-6">
                                        <div>
                                            <label for="brand_id" class="form-label">Select Brand</label>
                                            <select class="form-control" name="brand_id" id="brand_id" required>
                                                <option value="">-- Select Brand --</option>
                                                @foreach($brandData as $brand)
                                                    <option value="{{ $brand->id }}" 
                                                        {{ !empty($getcategoryInfo->brand_id) && $getcategoryInfo->brand_id == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                  
                                  
                                <!--<div class="col-xxl-6 col-md-6">-->
                                     
                                <!--    <label for="validationTextarea" class="form-label">Description</label>-->
                                <!--    <textarea class="form-control"  name="description"  id="basiInput" value="{{!empty($getcategoryInfo->description)?$getcategoryInfo->description:''}}" placeholder="Required example textarea" required="">{{!empty($getcategoryInfo->description)?$getcategoryInfo->description:''}}</textarea>-->
                                <!--    <div class="invalid-feedback">-->
                                <!--        Please enter a description -->
                                <!--    </div>-->
                                                
                                <!--  </div>-->
                                
                                
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control">{{ !empty($getcategoryInfo->description) ? $getcategoryInfo->description : '' }}</textarea>

                           
                               
                            
                        </div>
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