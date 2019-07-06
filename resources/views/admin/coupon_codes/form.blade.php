<div class="form-group">
    <label for="title" class="col-sm-3 control-label">{{ __('messages.coupon_code') }}</label>

    <div class="col-sm-9">
        <input id="title" type="text" placeholder="{{ __('messages.coupon_code') }}" class="form-control" name="coupon_code" value="{{ old('coupon_code') }}" required autofocus>
    </div>
</div>

<div class="form-group">
    <label for="coupon_type" class="col-sm-3 control-label">{{ __('messages.coupon_type') }}</label>

    <div class="col-sm-9 flair-radio">
        <input type="radio" name="coupon_type" value="1" checked> {{ __('messages.percentage') }} &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="coupon_type" value="2"> {{ __('messages.amount') }}        
    </div>
</div>

<div class="form-group">
    <label for="coupon_amount" class="col-sm-3 control-label">{{ __('messages.coupon_value') }}</label>

    <div class="col-sm-9">
        <input id="coupon_amount" type="text" placeholder="{{ __('messages.coupon_value') }}" class="form-control" name="coupon_amount" value="{{ old('coupon_amount') }}" required>
    </div>
</div>