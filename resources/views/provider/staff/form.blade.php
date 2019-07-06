<div class="form-group">
    <label for="name" class="col-sm-3 control-label">{{ __('messages.full_name') }}</label>

    <div class="col-sm-9">
        <input id="name" type="text" placeholder="{{ __('messages.full_name') }}" class="form-control" name="name" value="{{ old('name') }}" required autofocus>          
    </div>
</div>

<div class="form-group">
    <label for="email" class="col-sm-3 control-label">{{ __('messages.email_address') }}</label>

    <div class="col-sm-9">
        <input id="email" type="text" placeholder="{{ __('messages.email_address') }}" class="form-control" name="email" value="{{ old('email') }}" required>          
    </div>
</div>

<div class="form-group">
    <label for="phone" class="col-sm-3 control-label">{{ __('messages.phone') }} #</label>

    <div class="col-sm-9">
        <input id="phone" type="text" placeholder="{{ __('messages.phone') }} #" class="form-control" name="phone" value="{{ old('phone') }}" required {{ ($act == 'edit')?'disabled':'' }}>          
    </div>
</div>

<div class="form-group">
    <label for="profession" class="col-sm-3 control-label">{{ __('messages.profession') }} #</label>

    <div class="col-sm-9">
        <input id="profession" type="text" placeholder="{{ __('messages.profession') }} #" class="form-control" name="profession" value="{{ old('profession') }}">
    </div>
</div>

<div class="form-group" >
    <label for="profile_image" class="col-sm-3 control-label">{{ __('messages.photo_upload') }}</label>

    <div class="col-sm-9">
        <input type="file" id="profile_image" name="profile_image" class="form-control">
    </div>
</div>

@php 
$_loggedInUser = Auth::guard('web')->user(); 
@endphp

@if($_loggedInUser->user_type == 0)
<div class="form-group supervisor-align" >
    <label for="isAdmin">
        <input type="checkbox" id="isAdmin" name="isAdmin" {{ old('isAdmin') ? 'checked' : '' }}> {{ __('messages.supervisor') }}
    </label>
</div>
@endif

<h3>{{ __('messages.services') }}</h3>

<div class="form-group" >
    @foreach($shop_services as $service)
    <label for="service[{{ $service->category_id }}][{{ $service->service_id }}]" class="col-sm-4">
        <input type="checkbox" id="service[{{ $service->category_id }}][{{ $service->service_id }}]" name="service[{{ $service->category_id }}][{{ $service->service_id }}]" {{ old('man') ? 'checked' : '' }}> {{ $service->name }}
    </label>
    @endforeach
</div>