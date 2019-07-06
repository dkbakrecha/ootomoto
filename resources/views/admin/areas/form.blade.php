<div class="form-group">
    <label for="name" class="col-sm-3 control-label">{{ __('messages.area') }}</label>

    <div class="col-sm-9">
        <input id="name" type="text" placeholder="{{ __('messages.area') }}" class="form-control" name="name" value="{{ old('name') }}" required autofocus>          
    </div>
</div>

<div class="form-group">
    <label for="address" class="col-sm-3 control-label">{{ __('messages.address') }}</label>

    <div class="col-sm-9">
        <input id="address" type="text" placeholder="{{ __('messages.address') }}" class="form-control" name="address" value="{{ old('address') }}" required>          
    </div>
</div>


