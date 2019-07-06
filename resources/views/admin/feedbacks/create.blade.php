@extends('admin.layouts.app')

@section('sectionTitle', __('messages.feedback'))

@section('sectionButtons')

@endsection

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')

<div class="panel broadcast">
    <form action="{{ route('admin.feedback.create') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('messages.broadcast') }}</label>
                <div class="col-sm-9 col-md-7 col-lg-7">
                    <div class="check-text">
                        <input type="checkbox" class='boardcast' name="broadcast[]" value="0"> {{ __('messages.service_provider') }}
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" class='boardcast' name="broadcast[]" value="1"> {{ __('messages.supervisor') }}       
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" class='boardcast' name="broadcast[]" value="2"> {{ __('messages.customer') }}        
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('messages.select_area') }}</label>
                <div class="col-sm-9 col-md-7 col-lg-7">
                    <select name="area_id" id="area_id" class="form-control">
                        <option value="" selected>{{ __('messages.select_area') }}</option>
                        @foreach($areaList as $key => $area)
                        <option value="{{ $key }}">{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('messages.select_users') }}</label>
                <div class="col-sm-9 col-md-7 col-lg-7">
                    <select multiple="multiple" name="users_id[]" id="users_id" class="form-control select2" style="width: 100%;">
                        @foreach($users as $key => $user)
                        <option value="{{ $key }}" {{ (collect(old('services'))->contains($key)) ? 'selected':'' }}>{{ $user }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('messages.message') }}</label>
                <div class="col-sm-9 col-md-7 col-lg-7 feedback-msg">
                    <textarea placeholder="{{ __('messages.message') }}" id="message" class="form-control" name="message" required=""></textarea>
                </div>
            </div> 

            <div class="form-group">
                <label class="col-sm-2 col-sm-offset-1 control-label"></label>
                <div class="col-sm-5 col-sm-offset-3 user-buttons">
                    <button type="submit" class="btn btn-send pull-left">{{ __('messages.send') }}</button>
                    <a href="{{ route('admin.feedbacks') }}" type="button" class="btn btn-feed pull-left">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </div> 

        </div>


    </form>
</div>

@endsection

@section('page-js-script')
<script type="text/javascript">

    $(document).ready(function () {
        $('select#area_id').on('change', function () {
            if ($(this).val() == "") {
                $('#users_id').prop('disabled', false);
                $('input[name="broadcast[]"]').prop("disabled", false);
            } else {
                $('#users_id').prop('disabled', true);
                $('input[name="broadcast[]"]').prop("disabled", true);
            }

        });

        $('.boardcast').on('ifChecked', function () {
            $('#users_id').prop('disabled', true);
            $('#area_id').prop('disabled', true);
        });

        $('.boardcast').on('ifUnchecked', function (event) {
            if ($('.boardcast').filter(':checked').length == 0) {
                $('#users_id').prop('disabled', false);
                $('#area_id').prop('disabled', false);
            }
        });

        $("#users_id").change(function () {
            if ($(this).val() == "") {
                $('.boardcast').prop('disabled', false);
                $('#area_id').prop('disabled', false);
            } else {
                $('.boardcast').prop('disabled', true);
                $('#area_id').prop('disabled', true);
            }
        })

    });
</script>
@endsection