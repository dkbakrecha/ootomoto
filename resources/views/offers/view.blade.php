<div class="form-group">
    <label for="title" class="col-sm-3 control-label">{{ __('messages.offer_title') }}</label>

    <div class="col-sm-9">
        <input id="title" type="text" placeholder="{{ __('messages.offer_title') }}" class="form-control" name="title" value="{{ old('title') }}" disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="description" class="col-sm-3 control-label">{{ __('messages.offer_description') }}</label>

    <div class="col-sm-9">
        <input id="description" type="text" placeholder="{{ __('messages.offer_description') }}" class="form-control" name="description" value="{{ old('description') }}"  disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="services" class="col-sm-3 control-label">{{ __('messages.services') }}</label>
    <div class="col-sm-9">
        <input name="services" id="services" class="form-control"  disabled="true">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('messages.offer_image') }}</label>

    <div class="col-sm-9">
        <div id="previewImage"></div>
    </div>
</div>

<div class="form-group">
    <label for="price" class="col-sm-3 control-label">{{ __('messages.price') }}</label>

    <div class="col-sm-9">
        <input id="price" type="text" placeholder="{{ __('messages.price') }}" class="form-control" name="price" value="{{ old('price') }}"  disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="days" class="col-sm-3 control-label">{{ __('messages.time_duration') }}</label>

    <div class="col-sm-9">
        <input id="days" type="text" placeholder="{{ __('messages.time_duration') }}" class="form-control" name="days" value="{{ old('days') }}"  disabled="true">
    </div>
</div>