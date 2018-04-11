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

    Route::get('token','TokenController@getAPIToken');

    Route::get('airport','AirportController@getAirport');
    
    Route::get('search_flight', 'FlightController@search');

    Route::get('check_update','FlightController@checkUpdate');

    Route::get('lion_captcha','FlightController@getLionCaptcha');

    Route::get('flight_data','FlightController@getFlightData');

    Route::post('add_order','OrderController@addOrder');

    Route::get('order_detail','OrderController@orderDetail');

    Route::delete('delete_order/{id}','OrderController@deleteOrder');
    
    Route::get('checkout_page/{order_id}','OrderController@checkoutPage');

    Route::get('checkout_login','OrderController@checkoutLogin');

    Route::get('check_order','OrderController@checkOrder');

    Route::get('cc_payment','PaymentController@ccPayment');

    Route::get('klikBCA_payment','PaymentController@klikBCA');

    Route::get('order_history/{id}','OrderController@orderHistory');

    Route::get('orderdetail_history/{id}','OrderController@orderDetailHistory');

    Route::post('edit_user/{id}','UserController@store');

    Route::get('view_user/{id}','UserController@show');
});