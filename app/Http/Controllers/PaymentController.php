<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class PaymentController extends Controller
{
    public function ccPayment(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $checkouttoken = $request->checkouttoken;

        $result = $client->get('http://sandbox.tiket.com/payment/checkout_payment', [
            'query' => [
                'checkouttoken' => $checkouttoken
            ],
            'on_stats' => function (TransferStats $stats) use (&$url) {
                $url = $stats->getEffectiveUri();
            }
        ]);

        $body = $result->getBody()->getContents();
        return $url;
    }

    public function klikBCA(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $token = $request->token;

        $result = $client->get('https://api-sandbox.tiket.com/checkout/checkout_payment/3', [
            'query' => [
                'token' => env('TIKET_SECRET', '')
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }
}
