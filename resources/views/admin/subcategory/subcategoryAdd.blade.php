@extends('admin.layout.layout')
@section('content')

    <div class="container-fluid mt-5">

                     <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0"></h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                                        <li class="breadcrumb-item active"></li>
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
             
            
            
              @php
                  $action = isset($city)
                     ? route('SubcategoryUpdate', $city->id)
                     : route('subcategorySave');
            @endphp
 <form action="{{$action}}" method="post">
                @csrf
                @if(isset($city))
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
                                                 <label for="basiInput" class="form-label">Category</label>
                                                <select class="form-select mb-3" name="category_id" aria-label="Default select example" required>
                                                    <option selected="">Select</option>
                                                    
                                                   @if(sizeof($getState))
                                                        @foreach($getState as $state)
                                                            <option value="{{ $state->id }}" {{ (isset($city->category_id) && $city->category_id == $state->id) ? 'selected' : '' }}>
                                                                {{ $state->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                           

                                           
                                           <div class="col-lg-4">
                                                <div>
                                                    <label for="basiInput" class="form-label">Subcategory</label>
                                                    <input type="text" name="name" class="form-control"  value="{{isset($city->name)?$city->name:''}}" id="basiInput" required>
                                                </div>
                                            </div>
                                            
                                             <div class="col-lg-4 mt-4">
                                                 <div class="col-sm-auto">
                                        <div>
                                            @if(isset($city->id))
                                            <button type="submit" class="btn btn-success add-btn" data-bs-toggle="" id="create-btn" data-bs-target=""><i class="ri-add-line align-bottom me-1"></i> UPDATE</button>
                                            @else
                                            <button type="submit" class="btn btn-success add-btn" data-bs-toggle="" id="create-btn" data-bs-target=""><i class="ri-add-line align-bottom me-1"></i> ADD</button>
                                            @endif
                                        </div>
                                    </div>
                                            </div>


                                           
                                        </div>
                                        
                                  <!--      <div class="row">-->
                                  <!--           <div class="col-lg-4 mt-4">-->
                                     
                                  <!--  <label for="validationTextarea" class="form-label">Description</label>-->
                                  <!--  <textarea class="form-control"  name="description"  id="basiInput" value="{{!empty($city->description)?$city->description:''}}" placeholder="Required example textarea" required="">{{!empty($city->description)?$city->description:''}}</textarea>-->
                                  <!--  <div class="invalid-feedback">-->
                                  <!--      Please enter a description in the textarea.-->
                                  <!--  </div>-->
                                                
                                  <!--</div>-->
                                  <!--      </div>-->
                                        
                                         <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control">{{ !empty($city->description) ? $city->description : '' }}</textarea>

                           
                               
                            
                        </div>
                                    </div>
                                    
                                    
                                </div>
                                
                            </div>
                            
                            
                        </div> <!-- end col -->
                    </div>
                    </form>
</div>
@endsection