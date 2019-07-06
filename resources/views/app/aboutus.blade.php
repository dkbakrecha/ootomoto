@extends('layouts.baiscapp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1> Flair Is Saudi Booking App By Horizon I.T. </h1>
            <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
            <div class="app-link">
                <object data="{{ asset('images/globe.svg') }}" class="link-svg" type="image/svg+xml" height="16px" width="16px">
                    <img src="globe.svg" />
                </object>
                <a href="http://flair-app.com/" target="_BLANK">flair-app.com</a>
            </div>
        </div>
    </div>
</div>
@endsection