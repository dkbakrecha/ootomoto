<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

Route::post('signup', 'Auth\ApiController@register');
Route::post('user_verify', 'Auth\ApiController@user_verify');
Route::post('verify-otp', 'Auth\ApiController@verifyOTP');
Route::post('resend-otp', 'Auth\ApiController@resendOTP');
Route::post('login', 'Auth\ApiController@login');

Route::post('forgot_password', 'Api\ForgotPasswordController@sendToken');
Route::post('reset_password', 'Api\ForgotPasswordController@reset');

Route::post('shoplists', 'Auth\ApiController@shoplists');

Route::get('services', 'Auth\ApiController@services');
Route::get('vehicle_company', 'Auth\ApiController@vehicle_company');
Route::get('vehicle_model', 'Auth\ApiController@vehicle_model');
Route::get('content', 'Auth\ApiController@content');
Route::get('setting', 'Auth\ApiController@setting');

Route::get('offers', 'Api\OffersController@index');
Route::get('shop_profile', 'Api\UsersController@shop_profile');

Route::get('reviews', 'Api\ReviewsController@index');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('logout', 'Auth\ApiController@logout');
    Route::post('device_token', 'Auth\ApiController@device_token');
    Route::get('profile', 'Auth\ApiController@profile');
    Route::post('updatesetting', 'Auth\ApiController@updatesetting');
    Route::post('update_profile', 'Auth\ApiController@update_profile');
    Route::post('change_password', 'Api\UsersController@change_password');

    Route::get('vehicle', 'Api\VehicleController@index');
    Route::post('vehicle', 'Api\VehicleController@store');

    Route::get('favorite', 'Auth\ApiController@favorite');
    Route::post('shopfavorite', 'Auth\ApiController@shopfavorite');

    Route::get('cards', 'Api\CardController@index');
    Route::post('cards_store', 'Api\CardController@store');

    Route::post('message', 'Api\FeedbacksController@store');
    Route::get('messages', 'Api\FeedbacksController@index');

    Route::post('review', 'Api\ReviewsController@store');

    Route::post('booking', 'Api\BookingsController@store');
    Route::post('checkout', 'Api\BookingsController@checkout');
    Route::post('booking_token', 'Api\BookingsController@booking_token');
    Route::post('booking_check', 'Api\BookingsController@booking_chk');
    Route::post('reschedule', 'Api\BookingsController@reschedule');
    Route::get('reservations', 'Api\BookingsController@reservations');
    Route::get('reservation_details', 'Api\BookingsController@details');
    Route::post('reservation_cancel', 'Api\BookingsController@cancel');
});
