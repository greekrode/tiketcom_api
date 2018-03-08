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

Route::get('/token','TokenController@getAPIToken');

Route::get('/airport','AirportController@getAirport');

Route::get('/search_flight', 'FlightController@search');

Route::get('/login','CustomerController@login');

Route::post('/register','CustomerController@register');
