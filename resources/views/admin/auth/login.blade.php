<!-- Admin Login Form -->

@extends('admin.layouts.login')
@section('sectionTitle', __('Login Admin'))

@section('title', 'login here')

@section('content')
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <button class="close" data-dismiss="alert">&times;</button>
    <p>{{ $message }}</p>
</div>
@endif

<div class="login-center">
    <div class="login-box-body">
        <div class="login-logo">
            <a href="{{ route("admin.auth.login") }}"><img src="{{ url('images') }}/logo.png" width="50px"></b></a>
        </div>

        <p class="login-box-msg">Welcome back! Please login to your account.</p>

        <form class="" method="POST" action="{{ route('admin.auth.loginAdmin') }}">
            {{ csrf_field() }}


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
                <div class="col-xs-6" style="padding-right:0;">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> &nbsp; Remember me
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-6 verify">
                    <?php /* {{ route('password.request') }} */ ?>
                    <a class="btn btn-link" href="{{ route('admin.password.request') }}">
                        {{ __('Forgot Password') }}
                    </a>
                </div>
                <!-- /.col -->
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">
                        Login
                    </button>
                </div>
            </div>


        </form>

    </div>
</div>

@endsection