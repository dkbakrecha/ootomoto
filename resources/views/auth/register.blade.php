@extends('layouts.register')

@section('content')

<div class="col-lg-12">
    <p class="login-box-msg reset-pwd-heading register-heading">{{ __('Create Service Provider') }}</p>
    <form method="POST" id="registerSP" action="{{ route('register') }}" class="form-horizontal"  enctype="multipart/form-data">
        @csrf

        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">{{ __('messages.service_provider') }} <span class="required">*</span></label>

                    <div class="col-sm-9 reset-input">
                        <input id="name" type="text" placeholder="{{ __('messages.service_provider') }}" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="area_id" class="col-sm-3 control-label">{{ __('messages.area') }}</label>
                    <div class="col-sm-9 reset-input">
                        <select name="area_id" id="area_id" class="form-control">
                            <option value="" disabled selected>{{ __('messages.select_area') }}</option>
                            @foreach($areaList as $key => $area)
                            <option value="{{ $key }}" {{ (collect(old('area_id'))->contains($key)) ? 'selected':'' }}>{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="col-sm-3 control-label">{{ __('messages.address') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input id="address" type="text" placeholder="{{ __('messages.address') }}" class="form-control" name="address" value="{{ old('address') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="incharge_name" class="col-sm-3 control-label">{{ __('messages.in_charge') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input id="incharge_name" type="text" placeholder="{{ __('messages.in_charge') }}" class="form-control" name="incharge_name" value="{{ old('incharge_name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="col-sm-3 control-label">{{ __('messages.phone') }} <span class="required">*</span></label>

                    <div class="col-sm-9 reset-input">
                        <input id="phone" type="text" placeholder="{{ __('messages.phone') }}" class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="col-sm-3 control-label">{{ __('messages.email_address') }} <span class="required">*</span></label>

                    <div class="col-sm-9 reset-input">
                        <input id="email" type="text" placeholder="{{ __('messages.email_address') }}" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                </div>


                <div class="form-group">
                    <label for="services_for" class="col-sm-3 control-label">{{ __('messages.services_for') }}</label>

                    <div class="col-sm-9 reset-input">
                        <label for="man" style="margin-right: 15px;">
                            <input type="radio" class="service_for" id="man" name="service_mw" value="man" {{ old('women') ? 'checked' : '' }}> {{ __('messages.men') }}
                        </label>
                        <label for="women" style="margin-right: 15px;">
                            <input type="radio" class="service_for" id="women" name="service_mw" value="women" {{ old('women') ? 'checked' : '' }}> {{ __('messages.women') }}
                        </label>
                        <label for="kid" style="">
                            <input type="checkbox" class="service_for" id="kid" name="kid" {{ old('kid') ? 'checked' : '' }}> {{ __('messages.kids') }}
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="map" class="col-sm-3 control-label">{{ __('messages.map') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input id="map" type="text" placeholder="{{ __('messages.map') }}" class="form-control" name="map" value="{{ old('map') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment" class="col-sm-3 control-label">{{ __('messages.comment') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input id="comment" type="text" placeholder="{{ __('messages.comment') }}" class="form-control" name="comment" value="{{ old('comment') }}">
                    </div>
                </div>

            </div>
            <div class="col-sm-6 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="owner_name" class="col-sm-3 control-label">{{ __('messages.owner_name') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input id="owner_name" type="text" placeholder="{{ __('messages.owner_name') }}" class="form-control" name="owner_name" value="{{ old('owner_name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="owner_phone" class="col-sm-3 control-label">{{ __('messages.owner_phone') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input id="owner_phone" type="text" placeholder="{{ __('messages.owner_phone') }}" class="form-control" name="owner_phone" value="{{ old('owner_phone') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="crn" class="col-sm-3 control-label">{{ __('messages.commercial_registeration_number') }}#</label>

                    <div class="col-sm-9 reset-input">
                        <input id="crn" type="text" placeholder="{{ __('messages.commercial_registeration_number') }}#" class="form-control" name="crn" value="{{ old('crn') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="lincense" class="col-sm-3 control-label">{{ __('messages.license_number') }}#</label>

                    <div class="col-sm-9 reset-input">
                        <input id="lincense" type="text" placeholder="{{ __('messages.license_number') }}#" class="form-control" name="lincense" value="{{ old('lincense') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">{{ __('messages.services') }}</label>

                    <div class="col-sm-9 reset-input">
                        <select multiple="multiple" name="services[]" id="services" class="form-control select2" style="width: 100%;">
                            @foreach($services as $key => $service)
                            <option value="{{ $key }}" {{ (collect(old('services'))->contains($key)) ? 'selected':'' }}>{{ $service }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 

                <div class="form-group" >
                    <label for="comment" class="col-sm-3 control-label">{{ __('messages.photos') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input type="file" id="images" name="images[]" class="form-control" multiple>
                    </div>
                </div>

                <div class="form-group">
                    <label for="accept_payment" class="col-sm-3 control-label">{{ __('messages.payment_method') }}</label>

                    <div class="col-sm-9 reset-input">
                        <select name="accept_payment" id="accept_payment" class="form-control">
                            <option value="0">{{ __('messages.both') }}</option>
                            <option value="1">{{ __('messages.cash') }}</option>
                            <option value="2">{{ __('messages.card') }}</option>
                        </select>
                    </div>
                </div>


                

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">{{ __('messages.booking_approvel_mode') }}</label>

                    <div class="col-sm-9 reset-input">
                        <input type="radio" name="auto_approve" value="0" checked> {{ __('messages.auto') }}
                        <input type="radio" name="auto_approve" value="1"> {{ __('messages.manually') }}        
                    </div>
                </div>

            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-12 btn-create">
                <center>
                    <button type="submit" class="btn btn-primary register-btn">
                        {{ __('Create') }}
                    </button>
                </center>
            </div>
            <div class="col-md-12">
                <a href="{{ route("login") }}">Back to Sign In</a> | <a href="{{ route("appterms") }}">Terms of Use</a>
            </div>
        </div>
    </form>
</div>
@endsection
