<div class="form-group">
    <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.service_id') }}</label>

    <div class="col-sm-9">
        <input id="unique_id" type="text" placeholder="{{ __('messages.service_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" required disabled="">
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-3 control-label">{{ __('messages.services_name') }}</label>

    <div class="col-sm-9">
        @if($act == "update")
        <input id="name" type="text" placeholder="{{ __('messages.name') }}" class="form-control" name="name" value="{{ old('name') }}" required disabled="">
        @else
        <select name="service_id" id="service_id" class="form-control" style="width: 100%;" required="">
            <option value="" disabled selected>{{ __('messages.select_service') }}</option>
            @foreach($serviceMaster as $key => $category)
            <option value="{{ $key }}">{{ $category }}</option>
            @endforeach
        </select>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="duration" class="col-sm-3 control-label">{{ __('messages.duration_in_min') }}</label>

    <div class="col-sm-9">
        <input id="duration" type="text" placeholder="{{ __('messages.duration_in_min') }}" class="form-control" name="duration" value="{{ old('duration') }}" required>
    </div>
</div>

<div class="form-group">
    <label for="price" class="col-sm-3 control-label">{{ __('messages.price_in_sar') }}</label>

    <div class="col-sm-9">
        <input id="price" type="text" placeholder="{{ __('messages.price_in_sar') }}" class="form-control" name="price" value="{{ old('price') }}" required>
    </div>
</div>