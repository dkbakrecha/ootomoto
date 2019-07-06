<div class="form-group">
    <label for="name" class="col-sm-3 control-label">{{ __('messages.services_name') }}</label>

    <div class="col-sm-9">
        <input id="name" type="text" placeholder="{{ __('messages.services_name') }}" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-3 control-label">{{ __('messages.description') }}</label>

    <div class="col-sm-9">
        <input id="description" type="text" placeholder="{{ __('messages.description') }}" class="form-control" name="description" value="{{ old('description') }}" required autofocus>
    </div>
</div>

<div class="form-group">
    <label for="duration" class="col-sm-3 control-label">{{ __('messages.duration_in_min') }}</label>

    <div class="col-sm-9">
        <input id="duration" type="text" placeholder="{{ __('messages.duration') }}" class="form-control" name="duration" value="{{ old('duration') }}" required>
    </div>
</div>

<div class="form-group">
    <label for="price" class="col-sm-3 control-label">{{ __('messages.price') }} SAR</label>

    <div class="col-sm-9">
        <input id="price" type="text" placeholder="{{ __('messages.price') }}" class="form-control" name="price" value="{{ old('price') }}" required>
    </div>
</div>