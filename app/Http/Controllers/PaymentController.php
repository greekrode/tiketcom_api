<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class PaymentController extends Controller
{
    public function ccPayment(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $checkouttoken = $request->checkouttoken;

        $result = $client->get('http://sandbox.tiket.com/payment/checkout_payment', [
            'query' => [
                'checkouttoken' => $checkouttoken
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }

    public function klikBCA(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $token = $request->token;

        $result = $client->get('https://api-sandbox.tiket.com/checkout/checkout_payment/3', [
            'query' => [
                'token' => $token
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }
}
