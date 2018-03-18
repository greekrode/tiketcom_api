<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class OrderController extends Controller
{
    public function addOrder(Request $request){ 
        $client = new Client(); //GuzzleHttp\Client 
        $request->validate([ 
            'total_adult' => 'required', 
            'adults' => 'array|size:' . $request->total_adult, 
            'adults.*.firstname' => 'required', 
            'adults.*.lastname' => 'required', 
            'adults.*.birthdate' => 'required', 
            'adults.*.id' => 'required', 
            'adults.*.title' => 'required|in:"Mr.","Ms.","Mrs."', 
        ]); 
        $query = array('adult' => $request->total_adult); 
        //add adult  
        foreach ($request->adults as $key => $value) { 
            $adult = [ 
                'firstnamea'.$key => $value['firstname'], 
                'lastnamea'.$key => $value['lastname'], 
                'birthdatea'.$key => $value['birthdate'], 
                'ida'.$key => $value['id'], 
                'titlea'.$key => $value['title'] 
            ]; 
            $query += $adult; 
        } 
        $result = $client->get('https://api-sandbox.tiket.com/order', [ 
            'query' => $query 
        ]); 
        return $query;
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

    public function checkoutLogin(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $salutation = $request->salutation;
        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $emailAddress = $request->emailAddress;
        $phone = $request->phone;
        $saveContinue = $request->saveContinue;
        $token = $request->token;
        $output = $request->output;

        $result = $client->get('https://api-sandbox.tiket.com/checkout/checkout_customer', [
            'query' => [
                'salutation' => $salutation,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'emailAddress' => $emailAddress,
                'phone'  => $phone,
                'saveContinue' => $saveContinue,
                'token' => $token,
                'output' => $output
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }
    
    public function checkOrder(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $email = $request->email;
        $order_id = $request->order_id;
        $secretkey = $request->secretkey;
        $output = $request->output;

        $result = $client->get('https://api-sandbox.tiket.com/order/checkout/'.$order_id.'/IDR', [
            'query' => [
                'email' => $email,
                'order_id' => $order_id,
                'secretkey' => $secretkey,
                'output' => $output
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }
}
