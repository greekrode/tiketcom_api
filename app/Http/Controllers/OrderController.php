<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class OrderController extends Controller
{
    public function addOrder(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $token = $request->token;
        $flight_id = $request->flight_id;
        $ret_flight_id = $request->ret_flight_id;
        $lioncaptcha = $request->lioncaptcha;
        $lionsessionid = $request->lionsessionid;
        $adult = $request->adult;
        $child = $request->child;
        $conSalutation = $request->title;
        $conFirstName = $request->firstName; //mandatory
        $conLastName = $request->lastName; //not mandatory
        $conPhone = $request->phone; //Don't forget to urlencode + into %2B in the GET parameter
        $conEmailAddress = $request->email; //mandatory
        $conOtherPhone = $request->otherPhone; //not mandatory
        
        $temp = (array) $request; 

        for ($i = 1; $i <= $adult; $i++){
            $ida =  $temp['ida'.$i];
            echo $ida;
            echo '<br>';
        };
    }

    public function orderDetail(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $token = $request->token;
        $output = $request->output;

        $result = $client->get('https://api-sandbox.tiket.com/order', [
            'query' => [
                'token' => $token,
                'output' => $output
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }

    public function deleteOrder(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $order_detail_id = $request->order_detail_id;
        $token = $request->token;
        $output = $request->output;

        $result = $client->get('https://api-sandbox.tiket.com/order/delete_order',[
            'query' => [
                'order_detail_id' => $order_detail_id,
                'token' => $token,
                'output' => $output
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }

    public function checkoutPage(Request $request, $order_id){
        $client = new Client(); //GuzzleHttp\Client
        $token = $request->token;
        $output = $request->output;

        $result = $client->get('https://api-sandbox.tiket.com/order/checkout/'.$order_id.'/IDR', [
            'query' => [
                'token' => $token,
                'output' => $output
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }
}
