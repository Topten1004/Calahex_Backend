<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Datetime;

class FuturesController extends Controller
{
    /**
     * Get the order list for certain pair of futures trade in the platform
     *
     * @return [json] order list json
     */
    public function futuresOrder(Request $request)
    {
        $request->validate([
            'pair' => 'required|string'
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://poloniex.com/public?command=returnOrderBook&currencyPair='.$request->pair.'&depth=50',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: __cfduid=d3d91721f808982b972b8c1803eb611991607276557'
        ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        if(!array_key_exists("asks", $response)){
            $curl = curl_init();

            $separator = "_";
            $pair = explode("_", $request->pair)[1].$separator.explode("_", $request->pair)[0];

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://poloniex.com/public?command=returnOrderBook&currencyPair='.$pair.'&depth=50',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: __cfduid=d3d91721f808982b972b8c1803eb611991607276557'
                ),
            ));

            $response = json_decode(curl_exec($curl), true);

            curl_close($curl);

            if(!array_key_exists("asks", $response)){
                return response()->json([
                    'message' => 'Invalid currency pair.'
                ], 401);
            }            
        }

        $ask_list = [];

        foreach($response['asks'] as $ask_val){
            $ask = array(
                "price" => $ask_val[0],
                "amount" => $ask_val[1],
                "total" => floatval($ask_val[0]) * floatval($ask_val[1]),
            );
            $ask_list[] = $ask;
        }

        $bid_list = [];

        foreach($response['bids'] as $bid_val){
            $bid = array(
                "price" => $bid_val[0],
                "amount" => $bid_val[1],
                "total" => floatval($bid_val[0]) * floatval($bid_val[1]),
            );
            $bid_list[] = $bid;
        }

        $result = array(
            "asks" => $ask_list, 
            "bids" => $bid_list,
        );

        return response()->json($result, 201);
    }   
    
    /**
     * Get the trade history list for certain pair of futures trade in the platform
     *
     * @return [json] trade history list json
     */
    public function futuresTrade(Request $request)
    {
        $request->validate([
            'pair' => 'required|string'
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://poloniex.com/public?currencyPair='.$request->pair.'&command=returnTradeHistory',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: __cfduid=d3d91721f808982b972b8c1803eb611991607276557'
        ),
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        if(!is_countable($response)){
            $curl = curl_init();

            $separator = "_";
            $pair = explode("_", $request->pair)[1].$separator.explode("_", $request->pair)[0];

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?currencyPair='.$pair.'&command=returnTradeHistory',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: __cfduid=d3d91721f808982b972b8c1803eb611991607276557'
            ),
            ));

            $response = json_decode(curl_exec($curl), false);

            curl_close($curl);

            if(!is_countable($response)){
                response()->json([
                    'message' => 'Invalid currency pair.'
                ], 401);
            }
        }

        $result = [];

        for($i = 0 ; $i < 50 ; $i ++){
            $data = [];
            $data['type'] = $response[$i]->type;

            $format = 'Y-m-d H:i:s';
            $date = DateTime::createFromFormat($format, $response[$i]->date);

            $data['time'] = $date->format('H:i:s');
            $data['price'] = $response[$i]->rate;
            $data['amount'] = $response[$i]->amount;
            $data['total'] = floatval($response[$i]->amount) * floatval($response[$i]->rate);
            $result[] = $data;
        }
        
        return response()->json($result, 201);
    }   

    /**
     * Get the futures collateralized list in the platform
     *
     * @return [json] futures collateralized list json
     */
    public function futuresList(Request $request)
    {
        $request->validate([
            'pair_end' => 'required|string'
        ]);

        

        return response()->json(['BTC', 'ETH', 'BSV', 'BCH', 'YFI', 'UNI', 'LINK'], 201);
    }   
}
