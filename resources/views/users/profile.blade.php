<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.service_provider_id') }}</label>

            <div class="col-sm-9">
                <input id="unique_id" type="text" placeholder="{{ __('messages.service_provider_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" disabled="">
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">{{ __('messages.service_provider') }}</label>

            <div class="col-sm-9">
                <input id="name" type="text" placeholder="{{ __('messages.service_provider') }}" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="area_id" class="col-sm-3 control-label">{{ __('messages.area') }}</label>
            <div class="col-sm-9">
                <select name="area_id" id="area_id" class="form-control" required="">
                    <option value="" disabled selected>{{ __('messages.select_area') }}</option>
                    @foreach($areaList as $key => $area)
                    <option value="{{ $key }}">{{ $area }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-sm-3 control-label">{{ __('messages.address') }}</label>

            <div class="col-sm-9">
                <input id="address" type="text" placeholder="{{ __('messages.address') }}" class="form-control" name="address" value="{{ old('address') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="incharge_name" class="col-sm-3 control-label">{{ __('messages.in_charge') }}</label>

            <div class="col-sm-9">
                <input id="incharge_name" type="text" placeholder="{{ __('messages.in_charge') }}" class="form-control" name="incharge_name" value="{{ old('incharge_name') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="phone" class="col-sm-3 control-label">{{ __('messages.phone') }}</label>

            <div class="col-sm-9">
                <input id="phone" type="text" placeholder="{{ __('messages.phone') }}" class="form-control" name="phone" value="{{ old('phone') }}" disabled="true">
            </div>
        </div>

        <div class="form-group">
            <label for="map" class="col-sm-3 control-label">{{ __('messages.map') }}</label>

            <div class="col-sm-9">
                <input id="map" type="text" placeholder="{{ __('messages.map') }}" class="form-control" name="map" value="{{ old('map') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="services_for" class="col-sm-3 control-label">{{ __('messages.services_for') }}</label>

            <div class="col-sm-9">
                <label for="man">
                    <input type="radio" id="man" name="service_mw" value="man" {{ old('man') ? 'checked' : '' }}> {{ __('messages.men') }}
                </label>
                <label for="women">
                    <input type="radio" id="women" name="service_mw" value="women" {{ old('women') ? 'checked' : '' }}> {{ __('messages.women') }}
                </label>
                <label for="kid">
                       <input type="checkbox" id="kid" name="kid" {{ old('kid') ? 'checked' : '' }}> {{ __('messages.kids') }}
                </label>
            </div>
        </div>



        <div class="form-group" >
            <label for="images" class="col-sm-3 control-label">{{ __('messages.photos') }}</label>

            <div class="col-sm-9">
                <input type="file" id="images" name="images[]" class="form-control" multiple>
                @if($act != 'create')
                <div class="">
                    <span id="image_label">View</span>
                    <div class="" id="previewImages"></div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="owner_name" class="col-sm-3 control-label">{{ __('messages.owner_name') }}</label>

            <div class="col-sm-9">
                <input id="owner_name" type="text" placeholder="{{ __('messages.owner_name') }}" class="form-control" name="owner_name" value="{{ old('owner_name') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="owner_phone" class="col-sm-3 control-label">{{ __('messages.owner_phone') }}</label>

            <div class="col-sm-9">
                <input id="owner_phone" type="text" placeholder="{{ __('messages.owner_phone') }}" class="form-control" name="owner_phone" value="{{ old('owner_phone') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="crn" class="col-sm-3 control-label">{{ __('messages.commercial_registeration_number') }}#</label>

            <div class="col-sm-9">
                <input id="crn" type="text" placeholder="{{ __('messages.commercial_registeration_number') }}#" class="form-control" name="crn" value="{{ old('crn') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="lincense" class="col-sm-3 control-label">{{ __('messages.license_number') }}#</label>

            <div class="col-sm-9">
                <input id="lincense" type="text" placeholder="{{ __('messages.license_number') }}#" class="form-control" name="lincense" value="{{ old('lincense') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 control-label">{{ __('messages.services') }}</label>

            <div class="col-sm-9">
                <select multiple="multiple" name="services[]" id="services" class="form-control select2" style="width: 100%;">
                    @foreach($services as $key => $service)
                    <option value="{{ $key }}">{{ $service }}</option>
                    @endforeach
                </select>
            </div>
        </div> 

        <div class="form-group">
            <label for="email" class="col-sm-3 control-label">{{ __('messages.email_address') }}</label>

            <div class="col-sm-9">
                <input id="email" type="text" placeholder="{{ __('messages.email_address') }}" class="form-control" name="email" value="{{ old('email') }}" disabled="">
            </div>
        </div>

        <div class="form-group">
            <label for="accept_payment" class="col-sm-3 control-label">{{ __('messages.payment_method') }}</label>

            <div class="col-sm-9">

                <select name="accept_payment" id="accept_payment" class="form-control" required="">
                    <option value="" disabled selected>{{ __('messages.select_payment_method') }}</option>
                    <option value="0">{{ __('messages.both') }}</option>
                    <option value="1">{{ __('messages.cash') }}</option>
                    <option value="2">{{ __('messages.card') }}</option>
                </select>

            </div>
        </div>       
    </div>
</div>

