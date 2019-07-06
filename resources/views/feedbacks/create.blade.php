@extends('admin.layouts.app')

@section('sectionTitle', __('messages.feedback'))

@section('sectionButtons')

@endsection

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')

<div class="panel broadcast">
    <form action="{{ route('feedback.create') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="panel-body">
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
                    <textarea placeholder="{{ __('messages.message') }}" id="message" class="form-control" name="message"></textarea>
                </div>
            </div> 

            <div class="form-group">
                <label class="col-sm-2 col-sm-offset-1 control-label"></label>
                <div class="col-sm-5 col-sm-offset-3 user-buttons">
                    <button type="submit" class="btn btn-send pull-left">{{ __('messages.send') }}</button>
                    <a href="{{ route('feedbacks') }}" type="button" class="btn btn-feed pull-left">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </div> 

        </div>


    </form>
</div>

@endsection