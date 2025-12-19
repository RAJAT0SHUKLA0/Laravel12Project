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
      $action = isset($getproduct)?route('ProductUpdate',[$getproduct->id]):route('ProductSave');
      @endphp
      <form action ="{{$action}}" method='post' class="row g-3 "  enctype="multipart/form-data"   >
         @csrf
         @if(isset($getproduct))
           @method('PUT')
         @else
           @method('POST')
         @endif
         <div class="row">
            <div class="col-lg-12">
               <div class="card">
                  <div class="card-header align-items-center d-flex">
                     <h4 class="card-title mb-0 flex-grow-1">Product Add</h4>
                  </div>
                  <!-- end card header -->
                  <div class="card-body">
                     <div class="live-preview">
                        <div class="row gy-4">
                           <div class="col-md-4 position-relative">
                              <label for="validationTooltip01" class="form-label"> name</label>
                              <input type="text" class="form-control" id="validationTooltip01" name="name" value="{{isset($getproduct->name)?$getproduct->name:''}}" required>
                              @if($errors->has('name'))
                              <div class="invalid-tooltip">
                                 {{ $errors->first('name') }}
                              </div>
                              @else
                              <div class="invalid-tooltip">
                                 name  is required.
                              </div>
                              @endif
                           </div>
                           <div class="col-md-4 position-relative">
                              <label for="validationTooltip02" class="form-label">Category</label>
                              <select class="form-select" name="category_id" id="validationTooltip04" onchange="getSubCategory(this.value,'{{route('getSubcategory')}}','{{ csrf_token() }}')" required>
                                 <option selected disabled value="">Choose...</option>
                                 @if(sizeof($getCategory))
                                 @foreach($getCategory as $category)
                                 <option value="{{isset($category->id)?$category->id:''}}" {{isset($getproduct->category_id) && $category->id==$getproduct->category_id ?'selected':''}}>{{isset($category->name)?$category->name:''}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if($errors->has('category_id'))
                              <div class="invalid-tooltip">
                                 {{ $errors->first('category_id') }}
                              </div>
                              @else
                              <div class="invalid-tooltip">
                                 category  is required.
                              </div>
                              @endif
                           </div>
                          
                           <div class="col-md-3 position-relative">
                              <label for="" class="form-label">image</label>
                              <input type="file" name="image" class="form-control" id="" >
                               @if(isset($getproduct->image))
                                        <div>
                                          <img src="{{asset('storage/uploads/product/'.$getproduct->image)}}"  width="100px" alt="">
                                        </div>
                                        @endif
                           </div>
                           <div class="col-md-4 position-relative">
                              <label for="validationTooltip01" class="form-label"> Hsn Code</label>
                              <input type="text" class="form-control" id="validationTooltip01" name="hsn_code"  value="{{isset($getproduct->hsn_code)?$getproduct->hsn_code:''}}">
                              @if($errors->has('hsn_code'))
                              <div class="invalid-tooltip">
                                 {{ $errors->first('hsn_code') }}
                              </div>
                              @else
                              <div class="invalid-tooltip">
                                 name  is required.
                              </div>
                              @endif
                           </div>
                            <div class="col-md-7 position-relative">
                              <label for="" class="form-label">description</label>
                              <textarea id="description" name="description" class="form-control" id="" >{{isset($getproduct->description)?$getproduct->description:''}}</textarea>
                              
                           </div>
                           @if(isset($getproduct) && sizeof($getDetails))
                           @foreach($getDetails as $details)
                               <input  type="hidden" name="node_id[]" value="{{isset($details->id)?$details->id:''}}"></input>
                               <div class="col-md-3 position-relative">
                                  <label for="validationTooltipUsername" class="form-label">Varient</label>
                                  <select class="form-select varient" name="varient_id[]"   id="validationTooltip04" required placeholder="select varient">
                                     <option selected disabled value=""> Choose...</option>
                                     @if(sizeof($getVarient))
                                     @foreach($getVarient as $varient)
                                     <option value="{{isset($varient->id)?$varient->id:''}}" {{isset($details->varient_id) && $varient->id==$details->varient_id ?'selected':''}}>{{isset($varient->name)?$varient->name.' '.$varient->unit->name:''}}</option>
                                     @endforeach
                                     @endif
                                  </select>
                                  @if($errors->has('varient_id'))
                                  <div class="invalid-tooltip">
                                     {{ $errors->first('varient_id') }}
                                  </div>
                                  @else
                                  <div class="invalid-tooltip">
                                     varient is required.
                                  </div>
                                  @enderror
                               </div>
                               <div class="col-md-3 position-relative">
                                  <label for="validationTooltip01" class="form-label">Retailer price</label>
                                  <input type="text" class="form-control" id="validationTooltip01" name="retailer_price[]" value="{{isset($details->retailer_price)?$details->retailer_price:''}}" required>
                                  @if($errors->has('price'))
                                  <div class="invalid-tooltip">
                                     {{ $errors->first('price') }}
                                  </div>
                                  @else
                                  <div class="invalid-tooltip">
                                     price  is required.
                                  </div>
                                  @endif
                               </div>
                               <div class="col-md-2 position-relative">
                                  <label for="validationTooltip01" class="form-label">Mrp</label>
                                  <input type="text" class="form-control" id="validationTooltip01" name="mrp[]" value="{{isset($details->mrp)?$details->mrp:''}}" required>
                                  @if($errors->has('price'))
                                  <div class="invalid-tooltip">
                                     {{ $errors->first('price') }}
                                  </div>
                                  @else
                                  <div class="invalid-tooltip">
                                     price  is required.
                                  </div>
                                  @endif
                               </div>
                               <div class="col-md-2 position-relative">
                                  <label for="validationTooltip01" class="form-label">GST%</label>
                                  <input type="text" class="form-control" id="validationTooltip01" name="gst[]"  value="{{isset($details->gst)?$details->gst:''}}">
                                  @if($errors->has('gst'))
                                  <div class="invalid-tooltip">
                                     {{ $errors->first('gst') }}
                                  </div>
                                  @else
                                  <div class="invalid-tooltip">
                                     name  is required.
                                  </div>
                                  @endif
                               </div>
                                <div class="col-md-2 position-relative" style="margin-top: 26px;">
                                    <label for="validationTooltip01" class="form-label">Delete This Varient</label><br>
                                            <a href="@encryptedRoute('deleteThisVarient',$details->id)" class="btn btn-danger btn-icon waves-effect waves-light">
                                            <i class="ri-delete-bin-5-line"></i>
                                            </a>
                                    </div>
                               
                                @endforeach
                                  <div class="col-md-2 position-relative" style="margin-top: 26px;">
                                          <label for="validationTooltip01" class="form-label">Add More Varient</label><br>
                                        <button type="button" class="btn btn-success btn-icon waves-effect waves-light" onclick="getmultiplevarientFeild('{{ route('getMultiVarientSection') }}','{{ csrf_token() }}')">
                                            <i class="ri-add-circle-line align-middle me-1"></i>
                                        </button>
                                    </div>
                           @else
                            <input  type="hidden" name="node_id[]" value=""></input>

                            <div class="col-md-3 position-relative">
                              <label for="validationTooltipUsername" class="form-label">Varient</label>
                              <select class="form-select varient" name="varient_id[]"   id="validationTooltip04" required placeholder="select varient">
                                 <option selected disabled value=""> Choose...</option>
                                 @if(sizeof($getVarient))
                                 @foreach($getVarient as $varient)
                                 <option value="{{isset($varient->id)?$varient->id:''}}" >{{isset($varient->name)?$varient->name.' '.$varient->unit->name:''}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if($errors->has('varient_id'))
                              <div class="invalid-tooltip">
                                 {{ $errors->first('varient_id') }}
                              </div>
                              @else
                              <div class="invalid-tooltip">
                                 varient is required.
                              </div>
                              @enderror
                           </div>
                           <div class="col-md-3 position-relative">
                              <label for="validationTooltip01" class="form-label">Retailer price</label>
                              <input type="text" class="form-control" id="validationTooltip01" name="retailer_price[]"  required>
                              @if($errors->has('price'))
                              <div class="invalid-tooltip">
                                 {{ $errors->first('price') }}
                              </div>
                              @else
                              <div class="invalid-tooltip">
                                 price  is required.
                              </div>
                              @endif
                           </div>
                           <div class="col-md-2 position-relative">
                              <label for="validationTooltip01" class="form-label">Mrp</label>
                              <input type="text" class="form-control" id="validationTooltip01" name="mrp[]"  required>
                              @if($errors->has('price'))
                              <div class="invalid-tooltip">
                                 {{ $errors->first('price') }}
                              </div>
                              @else
                              <div class="invalid-tooltip">
                                 price  is required.
                              </div>
                              @endif
                           </div>
                           <div class="col-md-2 position-relative">
                              <label for="validationTooltip01" class="form-label">GST%</label>
                              <input type="text" class="form-control" id="validationTooltip01" name="gst[]"  >
                              @if($errors->has('gst'))
                              <div class="invalid-tooltip">
                                 {{ $errors->first('gst') }}
                              </div>
                              @else
                              <div class="invalid-tooltip">
                                 name  is required.
                              </div>
                              @endif
                           </div>
                           <div class="col-md-2 position-relative" stye="margin-top: 26px;">
                                  <label for="validationTooltip01" class="form-label">Add More Varient</label><br>
                             <button type="button" class="btn btn-success btn-icon waves-effect waves-light" onclick="getmultiplevarientFeild('{{route('getMultiVarientSection')}}','{{ csrf_token()}}')"><i class="ri-add-circle-line align-middle me-1" ></i></button>
                           </div>
                           @endif
                           
                            <div class="appendvarient">
                                
                            </div>
                         
                           <div class="col-12">
                              <button class="btn btn-primary" type="submit">Submit</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
   </div>
   </form>
</div>
</div>
@endsection