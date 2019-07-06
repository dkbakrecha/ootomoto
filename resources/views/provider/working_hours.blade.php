@extends('layouts.app')


@section('sectionTitle', __('messages.shop_working_hours'))

@section('content')
@include('elements.top_header')
@include('elements.messages')
<div class="row">
    <div class="col-md-6">
        <form action="{{ route('working_hours.store') }}" method="post" class="form-horizontal" style="margin-top:12px;">
            {{ csrf_field() }}
            <table class="table table-bordered working-hours">
                <tr>
                    <th width="150px">{{ __('messages.day') }}</th>
                    <th>{{ __('messages.open') }}</th>
                    <th>{{ __('messages.close') }}</th>
                </tr>
                @foreach ($workingHours as $hours)
                <tr>
                    <td>
                        <div class="checkbox icheck">
                            <label>
                                <input type="hidden" name="workinghours[{{ $hours['shop_weekday'] }}][shop]" value="1">
                                <input class="weekdaycheckbox" type="checkbox" data-weekday="{{ $hours['shop_weekday'] }}" name="workinghours[{{ $hours['shop_weekday'] }}][is_open]" {{ ($hours['is_open'] == 1) ? 'checked' : '' }}> {{ $hours['shop_weekday'] }}
                            </label>
                        </div>
                    </td>
                    <td>
                        <input type="text" autocomplete="off" name="workinghours[{{ $hours['shop_weekday'] }}][shop_starttime]" class="form-control timepicker {{ $hours['shop_weekday'] }}-start" value="{{ $hours['shop_starttime'] }}" {{ ($hours['is_open'] == 1) ? '' : 'disabled' }}>
                    </td>
                    <td>
                        <input type="text" autocomplete="off" name="workinghours[{{ $hours['shop_weekday'] }}][shop_closetime]" class="form-control timepicker {{ $hours['shop_weekday'] }}-close" value="{{ $hours['shop_closetime'] }}" {{ ($hours['is_open'] == 1) ? '' : 'disabled' }}>
                    </td>
                </tr>
                @endforeach
            </table>
            <div class="user-hours">
                <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-js-script')
<script type="text/javascript">
    $(document).ready(function () {
        $('.timepicker').timepicker({
            showInputs: false,
            showMeridian: false,
        });

        $('.weekdaycheckbox').on('ifChanged', function (event) {
            var _weekday = $(this).data('weekday');

            if ($(this).prop('checked') == true) {
                $("." + _weekday + "-start").removeAttr("disabled");
                $("." + _weekday + "-close").removeAttr("disabled");

            } else {
                $("." + _weekday + "-start").attr("disabled", "disabled");
                $("." + _weekday + "-close").attr("disabled", "disabled");
            }
        });
    });
</script>
@endsection