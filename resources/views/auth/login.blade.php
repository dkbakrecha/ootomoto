<!-- Service Provider and Supervisor Login Form -->
@extends('layouts.login')

@section('content')
@section('sectionTitle', __('Login'))

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <button class="close" data-dismiss="alert">&times;</button>

    <p>{{ $message }}</p>
</div>
@endif

<div class="login-box-body shop-login">
    <div class="login-logo">
        <a href="{{ route("login") }}">
            <img src="{{ url('images') }}/logo.png" width="50px">
        </a>
    </div>
    <p class="login-box-msg">Welcome back! Please login to your account.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group  has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" placeholder="E-Mail Address" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" placeholder="Password" class="form-control" name="password" required>
            @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
            @endif
        </div>

        <div class="row">
            <div class="col-xs-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember me') }}
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-6 verify">
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ __('Forgot Password') }}
                </a>
            </div>
            <!-- /.col -->
        </div>

        <div class="row">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat">
                    {{ __('Login') }}
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <a class="btn btn-primary btn-block btn-flat" href="{{ route('register') }}">
                    {{ __('Create Service Provider Account') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 reset-width" style="margin-top: 25px;">
                <a href="{{ route("siteterms") }}">Terms of Use</a>
            </div>
        </div>
    </form>

</div>
@endsection