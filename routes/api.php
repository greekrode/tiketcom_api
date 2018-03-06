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

Route::get('/token/{method}&{secret}','TokenController@getAPIToken');

Route::get('/airport/{token}&{output}','AirportController@getAirport');

Route::get('/search_flight/{departure}&{arrival}&{date}&{ret_date}&{adult}
            &{child}&{infant}&{token}&{version}&{output}', 'FlightController@search');
