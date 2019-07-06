@extends('layouts.app')

@section('content')

@section('sectionTitle', __('messages.settings'))
@include('admin.elements.general_top')

@include('admin.elements.messages')

<div class="panel">
    <form action="{{ route('change_password.store') }}" method="post" class="form-horizontal">
        {{ csrf_field() }}
        <div class="panel-body">
            <div class="row">
                <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                    <label for="current_password" class="col-md-4 control-label">{{ __('messages.current_password') }}</label>

                    <div class="col-md-6">
                        <input id="current_password" type="password" class="form-control" name="current_password" required>

                        
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="new_password" class="col-md-4 control-label">{{ __('messages.new_password') }}</label>

                    <div class="col-md-6">
                        <input id="new_password" type="password" class="form-control" name="new_password" required>

                        
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="col-md-4 control-label">{{ __('messages.confirm_password') }}</label>

                    <div class="col-md-6">
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
        </div>    
    </form>

</div>



@endsection