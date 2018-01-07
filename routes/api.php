<?php

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

Route::post('/signup' , 'UserApiController@signup');
Route::post('/logout' , 'UserApiController@logout');

Route::post('/auth/facebook', 'Auth\SociWooginController@facebookViaAPI');
Route::post('/auth/google', 'Auth\SociWooginController@googleViaAPI');

Route::post('/forgot/password',     'UserApiController@forgot_password');
Route::post('/reset/password',      'UserApiController@reset_password');
Route::get('/terms','UserApiController@terms');

Route::group(['middleware' => ['auth:api']], function () {

	// user profile

	Route::post('/change/password' , 'UserApiController@change_password');

	Route::post('/update/location' , 'UserApiController@update_location');

	Route::get('/details' , 'UserApiController@details');

	Route::post('/update/profile' , 'UserApiController@update_profile');

	// chat
	Route::get('/chat' , 'UserApiController@chat_histroy');

	// services

	Route::get('/services' , 'UserApiController@services');

	// provider

	Route::post('/rate/provider' , 'UserApiController@rate_provider');

	// request

	Route::post('/send/request' , 'UserApiController@send_request');

	Route::post('/cancel/request' , 'UserApiController@cancel_request');
	
	Route::get('/request/check' , 'UserApiController@request_status_check');
	Route::get('/show/providers' , 'UserApiController@show_providers');

	// history

	Route::get('/trips' , 'UserApiController@trips');
	Route::get('upcoming/trips' , 'UserApiController@upcoming_trips');
	
	Route::get('/trip/details' , 'UserApiController@trip_details');
	Route::get('upcoming/trip/details' , 'UserApiController@upcoming_trip_details');

	// payment

	Route::post('/payment' , 'PaymentController@payment');

	Route::post('/add/money' , 'PaymentController@add_money');

	// estimated

	Route::post('/estimated/fare' , 'UserApiController@estimated_fare');

	// help

	Route::get('/help' , 'UserApiController@help_details');

	// promocode

	Route::get('/promocodes' , 'UserApiController@promocodes');

	Route::get('/notify/promocodes' , 'UserApiController@get_promocodes');

	Route::post('/promocode/add' , 'UserApiController@add_promocode');

	// card payment

    Route::resource('card', 'Resource\CardResource');

});
