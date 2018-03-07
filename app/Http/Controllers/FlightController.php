<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FlightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function search(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $departure = $request->d;
        $arrival = $request->a;
        $date = $request->date;
        $ret_date = $request->ret_date;
        $adult = $request->adult;
        $child = $request->child;
        $infant = $request->infant;
        $token = $request->token;
        $version = $request->v;
        $output = $request->output;
        if ($ret_date){
            $result = $client->get('https://api-sandbox.tiket.com/search/flight', [
                'query' => [
                    'd' => $departure,
                    'a' => $arrival,
                    'date' => $date,
                    'ret_date' => $ret_date,
                    'adult' => $adult,
                    'child' => $child,
                    'infant' => $infant,
                    'token' => $token,
                    'v' => $version,
                    'output' => $output,
                ]
            ]);
        }else{
            $result = $client->get('https://api-sandbox.tiket.com/search/flight', [
                'query' => [
                    'd' => $departure,
                    'a' => $arrival,
                    'date' => $date,
                    'adult' => $adult,
                    'child' => $child,
                    'infant' => $infant,
                    'token' => $token,
                    'v' => $version,
                    'output' => $output,
                ]
            ]);
        }

        $body = $result->getBody();
        return $body;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
