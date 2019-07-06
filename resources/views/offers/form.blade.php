<div class="form-group">
    <label for="title" class="col-sm-3 control-label">{{ __('messages.offer_title') }}</label>

    <div class="col-sm-9">
        <input id="title" type="text" placeholder="{{ __('messages.offer_title') }}" class="form-control" name="title" value="{{ old('title') }}" required autofocus>
    </div>
</div>

<div class="form-group">
    <label for="description" class="col-sm-3 control-label">{{ __('messages.offer_description') }}</label>

    <div class="col-sm-9">
        <input id="description" type="text" placeholder="{{ __('messages.offer_description') }}" class="form-control" name="description" value="{{ old('description') }}" required>
    </div>
</div>

<div class="form-group">
    <label for="services" class="col-sm-3 control-label">{{ __('messages.services') }}</label>
    <div class="col-sm-9">

        <select multiple="multiple" name="services[]" id="services" class="form-control select2"  style="width: 100%;">
            @foreach($shop_services as $key => $service)
            <option value="{{ $service->service_id }}">{{ $service->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group" >
    <label for="offer_image" class="col-sm-3 control-label">{{ __('messages.photo_upload') }}</label>

    <div class="col-sm-9">
        <div id="previewImage"></div>
        <input type="file" id="offer_image" name="offer_image" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="price" class="col-sm-3 control-label">{{ __('messages.price') }}</label>

    <div class="col-sm-9">
        <input id="price" type="text" placeholder="{{ __('messages.price') }}" class="form-control" name="price" value="{{ old('price') }}" required>
    </div>
</div>

<div class="form-group">
    <label for="days" class="col-sm-3 control-label">{{ __('messages.time_duration') }}</label>

    <div class="col-sm-9">
        <input id="days" type="text" placeholder="{{ __('messages.time_duration') }}" class="form-control" name="days" value="{{ old('days') }}" required>
    </div>
</div>