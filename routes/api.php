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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/login','CustomerController@login');

// Route::post('/register','CustomerController@register');

// Route::post('/register','Auth\RegisterController@registered');

// Route::post('/login','CustomerController@login');

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('recover', 'AuthController@recover');


Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('logout', 'AuthController@logout');

    // Route::get('test', function() {
    //     return response()->json(['foo' => 'bar']);
    // });

    Route::get('token','TokenController@getAPIToken');

    Route::get('airport','AirportController@getAirport');
    
    Route::get('search_flight', 'FlightController@search');

    Route::get('check_update','FlightController@checkUpdate');

    Route::get('lion_captcha','FlightController@getLionCaptcha');

    Route::get('flight_data','FlightController@getFlightData');

    Route::get('add_order','OrderController@addOrder');

    Route::get('order_detail','OrderController@orderDetail');

    Route::get('delete_order','OrderController@deleteOrder');
    
    Route::get('checkout_page/{order_id}','OrderController@checkoutPage');

});