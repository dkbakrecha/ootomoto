<div class="form-group">
    <label for="name" class="col-sm-3 control-label">{{ __('messages.full_name') }}</label>

    <div class="col-sm-9">
        <input {{ ($act=="view")?"disabled":"" }} id="name" type="text" placeholder="{{ __('messages.full_name') }}" class="form-control" name="name" value="{{ old('name') }}"  required {{ ($act == 'edit')?'disabled':'' }}>          
    </div>
</div>

<div class="form-group">
    <label for="email" class="col-sm-3 control-label">{{ __('messages.email_address') }}</label>

    <div class="col-sm-9">
        <input {{ ($act=="view")?"disabled":"" }} id="email" type="text" placeholder="{{ __('messages.email_address') }}" class="form-control" name="email" value="{{ old('email') }}"  required {{ ($act == 'edit')?'disabled':'' }}>          
    </div>
</div>

<div class="form-group">
    <label for="phone" class="col-sm-3 control-label">{{ __('messages.phone') }} #</label>

    <div class="col-sm-9">
        <input {{ ($act=="view")?"disabled":"" }} id="phone" type="text" placeholder="{{ __('messages.phone') }} #" class="form-control" name="phone" value="{{ old('phone') }}" required {{ ($act == 'edit')?'disabled':'' }}>          
    </div>
</div>

<div class="form-group supervisor-align">
    <label for="isAdmin" class="col-sm-offset-3 control-label">
        <input {{ ($act=="view")?"disabled":"" }} type="checkbox" id="isAdmin" name="isAdmin" {{ old('isAdmin') ? 'checked' : '' }}> {{ __('messages.supervisor') }}
    </label>
</div>