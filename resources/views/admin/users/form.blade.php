<div class="form-group">
    <label for="name" class="col-sm-3 control-label">Services Name</label>

    <div class="col-sm-9">
        <input id="name" type="text" placeholder="Services Name" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
    </div>
</div>

<div class="form-group">
    <label for="category_id" class="col-sm-3 control-label">Category</label>
    <div class="col-sm-9">
        <select name="category_id" id="category_id" class="form-control">
            @foreach($categoryList as $key => $category)
            <option value="{{ $key }}">{{ $category }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="duration" class="col-sm-3 control-label">Duration</label>

    <div class="col-sm-9">
        <input id="duration" type="text" placeholder="Duration" class="form-control" name="duration" value="{{ old('duration') }}" required>
    </div>
</div>

<div class="form-group">
    <label for="price" class="col-sm-3 control-label">Price</label>

    <div class="col-sm-9">
        <input id="price" type="text" placeholder="Price" class="form-control" name="price" value="{{ old('price') }}" required>
    </div>
</div>