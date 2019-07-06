@extends('layouts.login')

@section('content')
@section('sectionTitle', __('Forgot Password'))

<div class="login-box-body mobile-size">
    <div class="container reset-width">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <img src="{{ url('images') }}/logo.png" width="50px">
                    <div class="panel-heading reset-pwd-heading">{{ __('Enter your email to reset your password') }}</div>

                    <div class="panel-body">
                        @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                        @endif

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">


                                <div class="col-md-12 reset-input">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-Mail Address" required>

                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="col-md-12 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                </div>

                                <div class="col-md-12">
                                    <a href="{{ route("login") }}">Back to Sign In</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
