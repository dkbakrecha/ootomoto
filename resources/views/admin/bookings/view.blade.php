<div class="form-group">
    <label for="username" class="col-sm-3 control-label">{{ __('messages.username') }}</label>

    <div class="col-sm-9">
        <input id="username" type="text" placeholder="{{ __('messages.username') }}" class="form-control" name="username" value="{{ old('username') }}" disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="booking_date" class="col-sm-3 control-label">{{ __('messages.booking_date') }}</label>

    <div class="col-sm-9">
        <input id="booking_date" type="text" class="form-control" name="booking_date" value="{{ old('booking_date') }}"  disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="booking_time" class="col-sm-3 control-label">{{ __('messages.booking_time') }}</label>
    <div class="col-sm-9">
        <input name="booking_time" id="booking_time" class="form-control"  disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="service_provider" class="col-sm-3 control-label">{{ __('messages.service_provider') }}</label>

    <div class="col-sm-9">
        <input id="service_provider" type="text" class="form-control" name="service_provider" disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="staff" class="col-sm-3 control-label">{{ __('messages.staff') }}</label>

    <div class="col-sm-9">
        <input id="staff" type="text" class="form-control" name="staff" disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="services" class="col-sm-3 control-label">{{ __('messages.services') }}</label>

    <div class="col-sm-9">
        <input id="services" type="text" class="form-control" name="services" disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="payment" class="col-sm-3 control-label">{{ __('messages.payment') }}</label>

    <div class="col-sm-9">
        <input id="payment" type="text" class="form-control" name="payment" disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="payment_method" class="col-sm-3 control-label">{{ __('messages.payment_method') }}</label>

    <div class="col-sm-9">
        <input id="payment_method" type="text" class="form-control" name="payment_method" disabled="true">
    </div>
</div>

<div class="form-group">
    <label for="status" class="col-sm-3 control-label">{{ __('messages.status') }}</label>

    <div class="col-sm-9" style="margin-top:12px;">
        <span class="label label-primary"></span>
    </div>
</div>