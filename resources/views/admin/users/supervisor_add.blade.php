<div class="form-group">
    <label for="service_provider_id" class="col-sm-3 control-label">{{ __('messages.service_providers') }}</label>
    <div class="col-sm-9">
        <select name="service_provider_id" id="service_provider_id" class="form-control" required="" data-title="{{ __('messages.select_staff') }}">
            <option value="" disabled selected>{{ __('messages.select_service_provider') }}</option>
            @foreach($shopList as $key => $shop)
            <option value="{{ $key }}">{{ $shop }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="staff_id" class="col-sm-3 control-label">{{ __('messages.staff_list') }}</label>

    <div class="col-sm-9">
        <select name="staff_id" id="staff_id" class="form-control" required="">
            <option value="" disabled selected>{{ __('messages.select_staff') }}</option>
        </select>
        <span class="text-red" id="emptyStaff"></span>
    </div>
</div>