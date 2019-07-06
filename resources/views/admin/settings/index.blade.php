@extends('admin.layouts.app')

@section('content')

@section('sectionTitle', __('messages.setting'))
@include('admin.elements.general_top')

@include('admin.elements.messages')

<div class="panel">
    <form action="{{ route('admin.settings.store') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="panel-body settings-body">
            @php ($i = 1)

            @foreach ($settings as $setting)

            <div class="row">
                @if ($i != $setting->group_id)
                <p class="subtitle fancy">
                	<span class="label label-info">{{ __("messages.".str_replace(' ', '_',strtolower($setting->group_label))) }}
                	</span>
                </p>
				<hr>
                @endif
                @php ($i = $setting->group_id)

                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-1 control-label">{{ __('messages.'.$setting->unique_key) }}</label>
                    <div class="col-sm-8">
                        <input id="{{ $setting->unique_key }}" type="text" placeholder="{{ __('messages.'.$setting->unique_key) }}" class="form-control" name="{{ $setting->unique_key }}" value="{{ $setting->value }}" required>
                    </div>
                </div>       
            </div>

            @endforeach

     
		</div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
        </div>    
    </form>

</div>



@endsection