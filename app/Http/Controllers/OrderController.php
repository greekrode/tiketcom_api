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
            'adults.*.title' => 'required|in:"Tuan","Nyonya"',
            'childs' => 'array|size:' . $request->total_child,
            'childs.*.firstname' => 'required',
            'childs.*.lastname' => 'required',
            'childs.*.birthdate' => 'required',
            'childs.*.title' => 'required|in:"Mstr.","Miss."',
            'infants' => 'array|size:' . $request->total_infant,
            'infants.*.firstname' => 'required',
            'infants.*.lastname' => 'required',
            'infants.*.birthdate' => 'required',
            'infants.*.parent' => 'required',
            'title' => 'required|in:"Mr.","Ms.","Mrs."',
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ]);
        $query = [
            'adult' => $request->total_adult, 
            'child' => $request->total_child,
            'infant' => $request->total_infant,
            'flight_id' => $request->flight_id,
            'lioncaptcha' => $request->lioncaptcha,
            'lionsessionid' => $request->lionsessionid,
            'conSalutation' => $request->title,
            'conFirstName' => $request->firstName,
            'conLastName' => $request->lastName,
            'conPhone' => $request->phone,
            'conEmailAddress' => $request->email
        ];

        if($request->ret_flight_id) $query += ['ret_flight_id' => $request->ret_flight_id];

        //add adult 
        foreach ($request->adults as $key => $value) {
            $key++;
            $adult = [
                'firstnamea'.$key => $value['firstname'],
                'lastnamea'.$key => $value['lastname'],
                'birthdatea'.$key => $value['birthdate'],
                'ida'.$key => $value['id'],
                'titlea'.$key => $value['title'],
                'passportnationalitya'.$key => $value['passportnationality'],
                'dcheckinbaggagea1'.$key => $value['dcheckinbaggage']
            ];
            $query += $adult;
        }

        //add child
        if ($request->total_child){
            foreach ($request->childs as $key => $value) {
                $key++;
                $child = [
                    'firstnamec'.$key => $value['firstname'],
                    'lastnamec'.$key => $value['lastname'],
                    'birthdatec'.$key => $value['birthdate'],
                    'idc'.$key => $value['id'],
                    'titlec'.$key => $value['title'],
                    'passportnationalityc'.$key => $value['passportnationality'],
                    'dcheckinbaggagec1'.$key => $value['dcheckinbaggage']
                ];
                $query += $child;
            }
        }

        //add infant
        if ($request->total_infant){
            foreach ($request->infants as $key => $value) {
                $key++;
                $child = [
                    'firstnamei'.$key => $value['firstname'],
                    'lastnamei'.$key => $value['lastname'],
                    'birthdatei'.$key => $value['birthdate'],
                    'idi'.$key => $value['id'],
                    'parenti'.$key => $value['parent']
                ];
                $query += $child;
            }
        }

        $result = $client->get('https://api-sandbox.tiket.com/apiv1/payexpress', [
            'query' => [
                'method' => 'getToken',
                'secretkey' => env('SECRET_KEY', ''),
                'output' => env('TIKET_OUTPUT', 'json')
            ]
        ]);
        $token = json_decode($result->getBody()->getContents(), true)['token'];
        $query += [
            'token' => $token, 
            'output' => env('TIKET_OUTPUT', 'json'), 
        ];

        $validateresult = $client->get('https://api-sandbox.tiket.com/order/add/flight', [
            'query' => $query
        ]);
        
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

                if($request->total_adult) {
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
                    'salutation' => $request->title,
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'phone' => $request->phone,
                    'emailAddress' => $request->email,
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
                'token' => env('TIKET_SECRET', ''),
                'output' => env('TIKET_OUTPUT', 'json')
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
                'token' => env('TIKET_SECRET', ''),
                'output' => env('TIKET_OUTPUT', 'json')
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
                'output' => env('TIKET_OUTPUT', 'json')
            ]
        ]);

        $body = $result->getBody();
        return $body;
    }
}
