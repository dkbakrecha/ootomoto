<div class="form-group">
    <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.customer_id') }}</label>

    <div class="col-sm-9">
        <input id="unique_id" type="text" placeholder="{{ __('messages.customer_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" disabled>
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-3 control-label">{{ __('messages.customer_name') }}</label>

    <div class="col-sm-9">
        <input id="name" type="text" placeholder="{{ __('messages.customer_name') }}" class="form-control" name="name" value="{{ old('name') }}" disabled="">          
    </div>
</div>

<div class="form-group">
    <label for="gender" class="col-sm-3 control-label">{{ __('messages.gender') }}</label>

    <div class="col-sm-9 flair-radio">
        <input type="radio" name="gender" value="m" checked disabled=""> {{ __('messages.male') }} &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="gender" value="f" disabled=""> {{ __('messages.female') }}        
    </div>
</div>

<div class="form-group">
    <label for="email" class="col-sm-3 control-label">{{ __('messages.email_address') }}</label>

    <div class="col-sm-9">
        <input id="email" type="text" placeholder="{{ __('messages.email_address') }}" class="form-control" name="email" value="{{ old('email') }}" disabled="">          
    </div>
</div>

<div class="form-group">
    <label for="phone" class="col-sm-3 control-label">{{ __('messages.phone') }} #</label>

    <div class="col-sm-9">
        <input id="phone" type="text" placeholder="{{ __('messages.phone') }} #" class="form-control" name="phone" value="{{ old('phone') }}" disabled="">          
    </div>
</div>

<div class="form-group">
    <label for="area" class="col-sm-3 control-label">{{ __('messages.area') }}</label>

    <div class="col-sm-9">
        <input id="area" type="text" placeholder="{{ __('messages.area') }}" class="form-control" name="area" value="" disabled="">          
    </div>
</div>

<div class="form-group">
    <label for="address" class="col-sm-3 control-label">{{ __('messages.address') }}</label>

    <div class="col-sm-9">
        <input id="address" type="text" placeholder="{{ __('messages.address') }}" class="form-control" name="address" value="" disabled="">          
    </div>
</div>