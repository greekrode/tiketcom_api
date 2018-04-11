<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Order;
use App\OrderDetail;
use App\Passenger;
use Auth;

class OrderController extends Controller
{
    public function addOrder(Request $request){
        $client = new Client(); //GuzzleHttp\Client

        $result = $client->get('https://api-sandbox.tiket.com/apiv1/payexpress', [
            'query' => [
                'method' => 'getToken',
                'secretkey' => env('SECRET_KEY', ''),
                'output' => env('TIKET_OUTPUT', 'json')
            ]
        ]);
        $token = json_decode($result->getBody()->getContents(), true)['token'];

        $validateresult = $client->get('https://api-sandbox.tiket.com/order/add/flight', [
            'query' => [
                'token' => $token,
                'flight_id' => $request->flight_id,
                'adult' => $request->adult,
                'conSalutation' => $request->conSalutation,
                'conFirstName' => $request->conFirstName,
                'conLastName' => $request->conLastName,
                'conPhone' => $request->conPhone,
                'conEmailAddress' => $request->conEmailAddress,
                'firstnamea1' => $request->firstnamea1,
                'lastnamea1' => $request->lastnamea1,
                'birthdatea1' => $request->birthdatea1,
                'ida1' => $request->ida1,
                'titlea1' => $request->titlea1
            ]
        ]);
        $validateresult = json_decode($validateresult->getBody()->getContents(), true);
        
        $result = $client->get('https://api-sandbox.tiket.com/order', [
            'query' => [
                'token' => $token,
                'output' => env('TIKET_OUTPUT', 'json')
            ]
        ]);

        $result = json_decode($result->getBody()->getContents(), true);
        if (array_key_exists('myorder', $result)) {
            $order = new Order();
            $order->token = $token;
            $order->user_id = Auth::id();
            $order->total_price = $result['myorder']['total'];
            $order->total_tax = $result['myorder']['total_tax'];
            $order->total_without_tax = $result['myorder']['total_without_tax'];
            $order->count_installment = $result['myorder']['count_installment'];
            $order->discount = $result['myorder']['discount'];
            $order->discount_amount = $result['myorder']['discount_amount'];
            $order->trip = count($result['myorder']['data']) > 1 ? 1 : 0;
            $order->save();
            foreach($result['myorder']['data'] as $detail) {
                $orderDetail = new OrderDetail();
                $orderDetail->order()->associate($order);
                $orderDetail->uri = $detail['uri'];
                $orderDetail->order_price = $detail['customer_price'];
                $orderDetail->order_name = $detail['order_name'];
                $orderDetail->order_name_detail = $detail['order_name_detail'];
                $orderDetail->airlines_name = $detail['detail']['airlines_name'];
                $orderDetail->flight_number = $detail['detail']['flight_number'];
                $orderDetail->price_adult = $detail['detail']['price_adult'];
                $orderDetail->price_child = $detail['detail']['price_child'];
                $orderDetail->price_infant = $detail['detail']['price_infant'];
                $orderDetail->flight_date = $detail['detail']['real_flight_date'];
                $orderDetail->departure_time = $detail['detail']['departure_time'];
                $orderDetail->arrival_time = $detail['detail']['arrival_time'];
                $orderDetail->baggage_fee = $detail['detail']['baggage_fee'];
                $orderDetail->departure_airport_name = $detail['detail']['departure_airport_name'];
                $orderDetail->departure_city_name = $detail['detail']['departure_city_name'];
                $orderDetail->departure_city = $detail['detail']['departure_city'];
                $orderDetail->arrival_airport_name = $detail['detail']['arrival_airport_name'];
                $orderDetail->arrival_city_name = $detail['detail']['arrival_city_name'];
                $orderDetail->arrival_city = $detail['detail']['arrival_city'];
                $orderDetail->airlines_photo = $detail['order_photo'];
                $orderDetail->price = $detail['detail']['price'];
                $orderDetail->tax_and_charge = $detail['tax_and_charge'];
                $orderDetail->subtotal_and_charge = $detail['subtotal_and_charge'];
                $orderDetail->save();

                    foreach ($detail['detail']['passengers']['adult'] as $person) {
                        $passenger = new Passenger();
                        $passenger->orderDetail()->associate($orderDetail);
                        $passenger->type = $person['type'];
                        $passenger->first_name = $person['first_name'];
                        $passenger->last_name = $person['last_name'];
                        $passenger->title = $person['title'];
                        $passenger->identity_no = $person['id_number'];
                        $passenger->birthdate = $person['birth_date'];
                        $passenger->adult_index = $person['adult_index'];
                        $passenger->passport_no = $person['passport_no'];
                        $passenger->passport_expiry = $person['passport_expiry'];
                        $passenger->passport_issuing_country = $person['passport_issuing_country'];
                        $passenger->passport_nationality = $person['passport_nationality'];
                        $passenger->check_in_baggage = $person['check_in_baggage'];
                        $passenger->check_in_baggage_return = $person['check_in_baggage_return'];
                        $passenger->check_in_baggage_size = $person['check_in_baggage_size'];
                        $passenger->check_in_baggage_size_return = $person['check_in_baggage_size_return'];
                        $passenger->passport_issued_date = $person['passport_issued_date'];
                        $passenger->birth_country = $person['birth_country'];
                        $passenger->birth_country = $person['birth_country'];
                        $passenger->ticket_number = $person['ticket_number'];
                        $passenger->check_in_baggage_unit = $person['check_in_baggage_unit'];
                        $passenger->save();
                    }
            }
            $orderId = $result['myorder']['order_id'];
            $result = $client->get('https://api-sandbox.tiket.com/order/checkout/' . $orderId . '/IDR' , [
                'query' => [
                    'token' => $token
                ]
            ]);
            $result = $client->get('https://api-sandbox.tiket.com/checkout/checkout_customer/' , [
                'query' => [
                    'token' => $token,
                    'salutation' => $request->conSalutation,
                    'firstName' => $request->conFirstName,
                    'lastName' => $request->conLastName,
                    'phone' => $request->conPhone,
                    'emailAddress' => $request->conEmailAddress,
                    'saveContinue' => 2
                ]
            ]);
            return $order;
        } else {
            return $validateresult;
        }
    }

    public function orderDetail(Request $request){
        $client = new Client(); //GuzzleHttp\Client
        $token = $request->token;
        $output = $request->output;

        $result = $client->get('https://api-sandbox.tiket.com/order', [
            'query' => [
                'token' => $token,
                'output' => env('TIKET_OUTPUT', 'json')
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }

    public function deleteOrder($id){
        $delete = Order::find($id);
        $delete->delete();

        $delete = Order::get();
        return response()->json($delete->toArray());
    }

    public function checkoutPage(Request $request, $order_id){
        $client = new Client(); //GuzzleHttp\Client
        $token = $request->token;
        $output = $request->output;

        $result = $client->get('https://api-sandbox.tiket.com/order/checkout/'.$order_id.'/IDR', [
            'query' => [
                'token' => env('TIKET_SECRET', ''),
                'output' => env('TIKET_OUTPUT', 'json')
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
                'token' => env('TIKET_SECRET', ''),
                'output' => env('TIKET_OUTPUT', 'json')
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }


    public function orderHistory(Request $request, $id){
        $history = Order::find($id);
        if ($history == null) {
            echo "U FAILED. PLS TRY HARD !!!";
        }else {
            return response()->json($history->toArray());
        }
    }

    public function orderDetailHistory($id){
        $history = OrderDetail::find($id);
        if ($history == null) {
            echo "U FAILED. PLS TRY HARD !!!";
        }else {
            return response()->json($history->toArray());
        }
    }
}
