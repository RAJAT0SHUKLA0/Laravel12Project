<!--<div id="variant" class="row align-items-end mb-3">-->
<div class="variant-section row align-items-end mb-3">
    <input type="hidden" name="node_id[]" value="">

    
    <div class="col-md-3 position-relative">
        <label for="validationTooltipUsername" class="form-label">Varient</label>
        <select class="form-select varient" name="varient_id[]" id="validationTooltip04" required>
            <option selected disabled value="">Choose...</option>
            @if(sizeof($getVarient))
                @foreach($getVarient as $varient)
                    <option value="{{ $varient->id ?? '' }}" 
                        {{ isset($getproduct->varient_id) && $varient->id == $getproduct->varient_id ? 'selected' : '' }}>
                        {{ $varient->name ?? '' }} {{ $varient->unit->name ?? '' }}
                    </option>
                @endforeach
            @endif
        </select>
        @if($errors->has('varient_id'))
            <div class="invalid-tooltip">
                {{ $errors->first('varient_id') }}
            </div>
        @else
            <div class="invalid-tooltip">
                Varient is required.
            </div>
        @endif
    </div>

    
    <div class="col-md-3 position-relative">
        <label for="retailer_price" class="form-label">Retailer Price</label>
        <input type="text" class="form-control" name="retailer_price[]" id="retailer_price" required>
        @if($errors->has('price'))
            <div class="invalid-tooltip">
                {{ $errors->first('price') }}
            </div>
        @else
            <div class="invalid-tooltip">
                Price is required.
            </div>
        @endif
    </div>

    
    <div class="col-md-2 position-relative">
        <label for="mrp" class="form-label">MRP</label>
        <input type="text" class="form-control" name="mrp[]" id="mrp" required>
        @if($errors->has('price'))
            <div class="invalid-tooltip">
                {{ $errors->first('price') }}
            </div>
        @else
            <div class="invalid-tooltip">
                Price is required.
            </div>
        @endif
    </div>

    <div class="col-md-2 position-relative">
        <label for="gst" class="form-label">GST%</label>
        <input type="text" class="form-control" name="gst[]" id="gst">
        @if($errors->has('gst'))
            <div class="invalid-tooltip">
                {{ $errors->first('gst') }}
            </div>
        @else
            <div class="invalid-tooltip">
                GST is required.
            </div>
        @endif
    </div>

 
    <div class="col-md-2">
        <!--<button type="button" onclick="removeVariantRow('variant')" class="btn btn-danger btn-icon waves-effect waves-light">-->
        <!--    <i class="ri-delete-bin-5-line"></i>-->
        <!--</button>-->
        <button type="button" onclick="removeVariantRow(this)"
                    class="btn btn-danger btn-icon waves-effect waves-light" >
                    <i class="ri-delete-bin-5-line me-10" style="font-size:20px;"></i>
                </button>
    </div>
</div>


<script>
    // function removeVariantRow(id) {
    //     document.getElementById(id).remove();
    // }
    function removeVariantRow(button) {
        const section = button.closest('.variant-section');
        if (section) {
            section.remove();
        }
    }
</script>
