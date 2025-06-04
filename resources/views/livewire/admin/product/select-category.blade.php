<div wire:ignore>
    <div class="col-12 mb-3">
        <label class="form-label"
               for="organizerMultiple">{{__("product.category")}}</label>
        <select class="form-select  js-choice" id="organizerMultiple"
                multiple="multiple" size="1"
                required
                x-on:change="res = [];$event.target.selectedOptions.forEach(function(el){
                                            res.push(el.value)
                                        });$data.model.category = res"
                name="category[]"
                data-options='{"removeItemButton":true,"placeholder":true}'>
            @foreach($categories as $category)
                <option @if(in_array($category->id,$old_categories ?? [])) selected @endif
                value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
        </select>

        @error("category")
        <p class="!text-[var(--falcon-danger)]">
            {{$message}}
        </p>
        @enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label"
               for="organizerMultiple">{{__("product.subcategory")}}</label>
        <select class="form-select  js-choice" id="organizerMultiple"
                multiple="multiple" size="1"
                name="subcategory[]"
                x-on:change="res = [];$event.target.selectedOptions.forEach(function(el){
                                            res.push(el.value)
                                        });$data.model.subcategory = res"
                data-options='{"removeItemButton":true,"placeholder":true}'>
            @foreach($subcategories as $subcategory)
                <option @if(in_array($subcategory->id,$old_subcategories ?? [])) selected
                        @endif value="{{$subcategory->id}}">{{$subcategory->name}}</option>
            @endforeach
        </select>

        @error("subcategory")
        <p class="!text-[var(--falcon-danger)]">
            {{$message}}
        </p>
        @enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label"
               for="organizerMultiple">{{__("product.offer")}}</label>
        <select class="form-select  js-choice" id="organizerMultiple"
                multiple="multiple" size="1"
                name="subcategory[]"
                x-on:change="res = [];$event.target.selectedOptions.forEach(function(el){
                                            res.push(el.value)
                                        });$data.model.offers = res"
                data-options='{"removeItemButton":true,"placeholder":true}'>
            @foreach($subcategories as $subcategory)
                <option @if(in_array($subcategory->id,$offers ?? [])) selected
                        @endif value="{{$subcategory->id}}">{{$subcategory->name}}</option>
            @endforeach
        </select>

        @error("subcategory")
        <p class="!text-[var(--falcon-danger)]">
            {{$message}}
        </p>
        @enderror
    </div>
</div>
