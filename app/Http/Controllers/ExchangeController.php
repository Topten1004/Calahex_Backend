<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\ExchangeOrders;
use App\Models\Tokens;
use App\Models\Wallets;
use Illuminate\Http\Request;
use \Datetime;
use Carbon\Carbon;
class ExchangeController extends Controller
{
    /**
     * Get the order list for certain pair of exchange in the platform
     *
     * @return [json] order list json
     */
    public function exchangeOrder(Request $request)
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
        $ask_best = 100000000;
        $ask_sum_amount = 0;
        $ask_sum_total = 0;

        foreach($response['asks'] as $ask_val){
            $ask_sum_amount += $ask_val[1];
            $ask_sum_total += floatval($ask_val[0]) * floatval($ask_val[1]);
            $ask = array(
                "price" => $ask_val[0],
                "amount" => $ask_val[1],
                "total" => floatval($ask_val[0]) * floatval($ask_val[1]),
                "sum_amount" => $ask_sum_amount,
                "sum_total" => $ask_sum_total,
            );
            if($ask_best > floatval($ask_val[0]))
                $ask_best = floatval($ask_val[0]);
            $ask_list[] = $ask;
        }

        $bid_list = [];
        $bid_best = 0;
        $bid_sum_amount = 0;
        $bid_sum_total = 0;

        foreach($response['bids'] as $bid_val){
            $bid_sum_amount += $bid_val[1];
            $bid_sum_total += floatval($bid_val[0]) * floatval($bid_val[1]);
            $bid = array(
                "price" => $bid_val[0],
                "amount" => $bid_val[1],
                "total" => floatval($bid_val[0]) * floatval($bid_val[1]),
                "sum_amount" => $bid_sum_amount,
                "sum_total" => $bid_sum_total,
            );
            if($bid_best < floatval($bid_val[0]))
                $bid_best = floatval($bid_val[0]);
            $bid_list[] = $bid;
        }

        $from_token = explode('_', $request->pair)[1];
        $from_token_id = Tokens::where('token_symbol', $from_token)->first()->id;
        $to_token = explode('_', $request->pair)[0];
        $to_token_id = Tokens::where('token_symbol', $to_token)->first()->id;

        $buy_available = 0;
        $sell_available = 0;
        if(isset($request->user_id)){
            $wallet_id = Wallets::where('user_id', $request->user_id)->first()->id;
            $buy_account = Accounts::where('account_type', 'exchange')->where('token_id', $from_token_id)->where('wallet_id', $wallet_id)->first();
            if($buy_account)    $buy_available = $buy_account->amount;
            $sell_account = Accounts::where('account_type', 'exchange')->where('token_id', $to_token_id)->where('wallet_id', $wallet_id)->first();
            if($sell_account)    $sell_available = $sell_account->amount;
        }

        $result = array(
            "asks" => $ask_list,
            "ask_best" => $ask_best,
            "bids" => $bid_list,
            "bid_best" => $bid_best,
            "buy_available" => $buy_available,
            "sell_available" => $sell_available
        );

        return response()->json($result, 201);
    }

    /**
     * Get the trade history list for certain pair of exchange in the platform
     *
     * @return [json] trade history list json
     */
    public function exchangeTrade(Request $request)
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

        for($i = 0 ; $i < count($response) ; $i ++){
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
     * Get the crypto pair list of exchange in the platform
     *
     * @return [json] crypto pair list json
     */
    public function exchangeCryptoPair(Request $request)
    {
        $request->validate([
            'pair_end' => 'required|string'
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://poloniex.com/public?command=returnCurrencies',
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

        $response_currency_list = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair_info = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $crypto_pair_list = [];

        $tokens = Tokens::where('for_cefi', 1)->where('status',2)->get();

        foreach($tokens as $token){
            $token_pairs = explode(',',$token->token_pair_type);
            if($token->token_pair_type !="")
                for($i = 0;$i<count($token_pairs);$i++){
                    $pair = $token_pairs[$i].'_'.$token->token_symbol;

                    $pair_left = $token_pairs[$i];
                    $pair_right = $token->token_symbol;
                    $left_data = [];
                    $right_data = [];
                    foreach($response_currency_list as $key => $value){
                        if($key == $pair_left)  $left_data = $value;
                        if($key == $pair_right) $right_data = $value;
                    }
                    if($left_data){
                        $pair_left_name = $left_data["name"];
                        $pair_left_net = $left_data["blockchain"];
                    }
                    if($right_data){
                        $pair_right_name = $right_data["name"];
                        $pair_right_net = $right_data["blockchain"];
                    }

                    $separator = '_';
                    $pair_reverse = $pair_right.$separator.$pair_left;

                    // if(strpos($pair_left_name, 'Token') === false && strpos($pair_right_name, 'Token') === false){
                        if($request->pair_end == 'all'){
                            $data = [];
                            $data['name'] = $pair;
                            $data['fullname'] = $pair_left_name;
                            $pair_data = [];
                            foreach($response_pair_info as $key=>$value){
                                if($key == $pair)
                                    $pair_data = $value;
                            }
                            if($pair_data){
                                $data['price'] = $pair_data['last'];
                                $data['high'] = $pair_data['high24hr'];
                                $data['low'] = $pair_data['low24hr'];
                                $data['volume'] = $pair_data['baseVolume'];
                                $data['percent'] = $pair_data['percentChange'];
                                
                                $crypto_pair_list[] = $data;
                            }
                        }
                        else{
                            if($pair_left == $request->pair_end){
                                $data = [];
                                $data['name'] = $pair_reverse;
                                $data['fullname'] = $pair_right_name;
                                $pair_data = [];
                                foreach($response_pair_info as $key=>$value){
                                    if($key == $pair)
                                        $pair_data = $value;
                                }
                                if($pair_data){
                                    $data['price'] = $pair_data['last'];
                                    $data['high'] = $pair_data['high24hr'];
                                    $data['low'] = $pair_data['low24hr'];
                                    $data['volume'] = $pair_data['baseVolume'];
                                    $data['percent'] = $pair_data['percentChange'];
                                    $crypto_pair_list[] = $data;
                                }
                            }
                        }
                    // }
                }


        }

        return response()->json($crypto_pair_list, 201);
    }

    /**
     * Get the token pair list of exchange in the platform
     *
     * @return [json] token pair list json
     */
    public function exchangeTokenPair(Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair = json_decode(curl_exec($curl));

        curl_close($curl);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnCurrencies',
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

        $response_currency_list = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair_info = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $token_pair_list = [];

        foreach($response_pair as $pair => $value){
            $pair_left = explode('_', $pair)[0];
            $pair_left_name = $response_currency_list[$pair_left]["name"];
            $pair_left_net = $response_currency_list[$pair_left]["blockchain"];
            $pair_right = explode('_', $pair)[1];
            $pair_right_name = $response_currency_list[$pair_right]["name"];
            $pair_right_net = $response_currency_list[$pair_left]["blockchain"];

            if((strpos($pair_left_name, 'Token') !== false && $pair_left_net == 'ETH') || (strpos($pair_right_name, 'Token') !== false && $pair_right_net == 'ETH')){
                $data = [];
                $data['name'] = $pair;
                $data['fullname'] = $pair_left_name;
                $data['price'] = $response_pair_info[$pair]['last'];
                $data['high'] = $response_pair_info[$pair]['high24hr'];
                $data['low'] = $response_pair_info[$pair]['low24hr'];
                $data['volume'] = $response_pair_info[$pair]['baseVolume'];
                $data['percent'] = $response_pair_info[$pair]['percentChange'];
                $token_pair_list[] = $data;
            }
        }

        return response()->json($token_pair_list, 201);
    }

    public function exchangeInfo (Request $request){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair_info = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return response()->json($response_pair_info);
    }

    public function exchangeLimitAmount(Request $request){
        $id = $request->user_id;

        $wallet = Wallets::where('user_id',$id)->first();
        $wallet_id = $wallet->id;

        $accounts = Accounts::where('wallet_id',$wallet_id)->where('account_type','exchange')->orderBy('token_id', 'asc')->get();
        $result = [];
        foreach($accounts as $account) {
            $result[$account->token->token_symbol] = $account->amount;
        }
        return response()->json($result);
    }

    public function exchangeFeeAmount(Request $request){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair_info = json_decode(curl_exec($curl), true);
        curl_close($curl);

        $id = $request->user_id;

        $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('status',0)->whereDate('trade_at', '>', Carbon::now()->subDays(30))->get();
        $total = 0.00;
        foreach($exchangeOrders as $exchangeOrder){
            $coin = $exchangeOrder->token->token_symbol;
            $pair = "USDT_".$coin;
            $total += $response_pair_info[$pair]['last']*$exchangeOrder->amount;
        }
        return response()->json($total);
    }

    public function buyAmount(Request $request) {
        $id = $request->user_id;
        $toAmount = $request->toAmount;
        $toCoin = $request->toCoin;
        $fromAmount = $request->fromAmount;
        $fromCoin = $request->fromCoin;
        $wallet_id = Wallets::where('user_id',$id)->first()->id;
        $token_id = Tokens::where('token_symbol',$fromCoin)->first()->id;
        $account = Accounts::where('wallet_id',$wallet_id)->where('account_type', 'exchange')->where('token_id',$token_id)->first();
        if($account->amount < $fromAmount)  
            return response()->json("No sufficient fund", 201);
        $account->amount = $account->amount - $fromAmount;
        $account->save();

        $to_token_id = Tokens::where('token_symbol',$toCoin)->first()->id;
        $account = Accounts::where('wallet_id',$wallet_id)->where('account_type', 'exchange')->where('token_id',$to_token_id)->first();

        if(isset($account)){
            $account->amount = $account->amount + $toAmount;
            $account->save();
        }else {
            $account = New Accounts();
            $account->wallet_id = $wallet_id;
            $account->token_id = $to_token_id;
            $account->account_type = 'exchange';
            $account->amount = $toAmount;
            $account->save();
        }
        return response()->json("successfully done!", 201);
    }

    public function addOrder(Request $request){
        $id = $request->user_id;
        $toAmount = $request->toAmount;
        $toCoin = $request->toCoin;
        $unit_to = Tokens::where('token_symbol',$toCoin)->first()->id;

        $fromAmount = $request->fromAmount;
        $fromCoin = $request->fromCoin;
        $unit_from = Tokens::where('token_symbol',$fromCoin)->first()->id;
        $exchangePrice = $request->exchangePrice;
        $type = $request->type;
        $status = $request->status;
        $pay_type = $request->pay_type;
        $fee = $request->fee;

        $wallet_id = Wallets::where('user_id', $id)->first()->id;
        $account = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $toCoin)->first();

        $exchangeOrder = New ExchangeOrders();
        $exchangeOrder->user_id = $id;
        $exchangeOrder->limit_price = $exchangePrice;
        $exchangeOrder->amount = $fromAmount;
        $exchangeOrder->total = $toAmount;
        $exchangeOrder->type = $type;
        $exchangeOrder->unit_from = $unit_from;
        $exchangeOrder->unit_to = $unit_to;
        $exchangeOrder->fee = $fee;
        $exchangeOrder->status = $status;
        $exchangeOrder->filled = $account?$account->amount:0;
        $exchangeOrder->pay_type = $pay_type;
        $exchangeOrder->save();
    }

    public function userOrderList(Request $request){
        $id = $request->user_id;
        $toCoin = $request->toCoin;
        $price = $request->price;
        $unit_to = Tokens::where('token_symbol',$toCoin)->first()->id;
        $fromCoin = $request->fromCoin;
        $unit_from = Tokens::where('token_symbol',$fromCoin)->first()->id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair_info = json_decode(curl_exec($curl), true);

        curl_close($curl);


        // From Token
        $funds = [];
        $wallet_id = Wallets::where('user_id', $id)->first()->id;
        $from = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $unit_from)->first();
        if($from)   $from_available = $from->amount;
        else        $from_available = 0;
        $from_order = 0.0;
        $orders = ExchangeOrders::where('user_id', $id)->where('status', '0')->get();
        foreach($orders as $order){
            if($order->unit_from == $unit_from && $order->type == 'buy')
                $from_order = floatval($from_order) - floatval($order->amount);
            if($order->unit_from == $unit_from && $order->type == 'sell')
                $from_order = floatval($from_order) + floatval($order->total);
            if($order->unit_to == $unit_from && $order->type == 'sell')
                $from_order = floatval($from_order) - floatval($order->amount);            
            if($order->unit_to == $unit_from && $order->type == 'buy')
                $from_order = floatval($from_order) + floatval($order->total);
        }
        $from_total = floatval($from_available) + floatval($from_order);
        if($fromCoin != 'USDT' && $fromCoin != 'NMR' && $fromCoin != 'KNC' && $fromCoin != 'BNT' && $fromCoin != 'OMG')
            $rate = floatval($response_pair_info['USDT_'.$fromCoin]["last"]);
        if($fromCoin == 'NMR' || $fromCoin == 'KNC' || $fromCoin == 'BNT' || $fromCoin == 'OMG'){
            $rate = floatval($response_pair_info['BTC_'.$fromCoin]["last"]) * floatval($response_pair_info['USDT_BTC']["last"]);
        }
        if($fromCoin == 'USDT')
            $rate = 1;
        $fund = array(
            "symbol"=>$fromCoin,
            "total" => $from_total,
            "available" => $from_available,
            "order" => $from_order,
            "usdt_value" => floatval($rate) * floatval($from_total)
        );
        $funds[] = $fund;


        // To Token
        $to = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $unit_to)->first();
        if($to)     $to_available = $to->amount;
        else        $to_available = 0;
        $to_order = 0.0;
        $orders = ExchangeOrders::where('user_id', $id)->where('status', '0')->get();
        foreach($orders as $order){
            if($order->unit_from == $unit_to && $order->type == 'buy')
                $to_order = floatval($to_order) - floatval($order->amount);
            if($order->unit_from == $unit_to && $order->type == 'sell')
                $to_order = floatval($to_order) + floatval($order->total);
            if($order->unit_to == $unit_to && $order->type == 'sell')
                $to_order = floatval($to_order) - floatval($order->amount);            
            if($order->unit_to == $unit_to && $order->type == 'buy')
                $to_order = floatval($to_order) + floatval($order->total);      
        }
        $to_total = floatval($to_available) + floatval($to_order);
        if($toCoin != 'USDT' && $toCoin != 'NMR' && $toCoin != 'KNC' && $toCoin != 'BNT' && $toCoin != 'OMG')
            $rate = floatval($response_pair_info['USDT_'.$toCoin]["last"]);
        if($toCoin == 'NMR' || $toCoin == 'KNC' || $toCoin == 'BNT' || $toCoin == 'OMG'){
            $rate = floatval($response_pair_info['BTC_'.$toCoin]["last"]) * floatval($response_pair_info['USDT_BTC']["last"]);
        }
        if($toCoin == 'USDT')
            $rate = 1;
        $fund = array(
            "symbol"=>$toCoin,
            "total" => $to_total,
            "available" => $to_available,
            "order" => $to_order,
            "usdt_value" => floatval($rate) * floatval($to_total)
        );
        $funds[] = $fund;


        $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('unit_to',$unit_to)->where('unit_from',$unit_from)->where('type','not like','convert')->where('cleared', '0')->get();
        $result = [];
        foreach($exchangeOrders as $exchangeOrder){
            $data = [];
            $date = new DateTime($exchangeOrder->created_at);
            $data["datetime"] = $date->format('m-d H:i:s');
            $data["price"] = $exchangeOrder->limit_price;
            $data["amount"] = $exchangeOrder->amount;
            $data["total"] = $exchangeOrder->total;
            $data["status"] = $exchangeOrder->status;
            $data["type"] = $exchangeOrder->pay_type;
            $data["side"] = $exchangeOrder->type;
            $data["fee"] = $exchangeOrder->fee;
            $data["filled"] = $exchangeOrder->filled?$exchangeOrder->filled:0;
            $token1 = Tokens::find($exchangeOrder->unit_from)->token_symbol;
            $token2 = Tokens::find($exchangeOrder->unit_to)->token_symbol;
            $pair = ($exchangeOrder->type=='buy'?$token2:$token1)."/".($exchangeOrder->type=='buy'?$token1:$token2);
            $data["pair"] = $pair;
            $data["id"] = $exchangeOrder->id;
            $result[] = $data;
        }

        $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('unit_to',$unit_from)->where('unit_from',$unit_to)->where('type','not like','convert')->where('cleared', '0')->get();
        foreach($exchangeOrders as $exchangeOrder){
            $data = [];
            $date = new DateTime($exchangeOrder->created_at);
            $data["datetime"] = $date->format('m-d H:i:s');
            $data["price"] = $exchangeOrder->limit_price;
            $data["amount"] = $exchangeOrder->amount;
            $data["total"] = $exchangeOrder->total;
            $data["status"] = $exchangeOrder->status;
            $data["type"] = $exchangeOrder->pay_type;
            $data["side"] = $exchangeOrder->type;
            $data["fee"] = $exchangeOrder->fee;
            $data["filled"] = $exchangeOrder->filled?$exchangeOrder->filled:0;
            $token1 = Tokens::find($exchangeOrder->unit_from)->token_symbol;
            $token2 = Tokens::find($exchangeOrder->unit_to)->token_symbol;
            $pair = ($exchangeOrder->type=='buy'?$token2:$token1)."/".($exchangeOrder->type=='buy'?$token1:$token2);
            $data["pair"] = $pair;
            $data["id"] = $exchangeOrder->id;
            $result[] = $data;
        }

        usort($result, function($a, $b){
            return strcmp($b["datetime"], $a["datetime"]);
        });

        $results = array(
            "result" => $result,
            "fund" => $funds
        );

        return response()->json($results);
    }

    public function executeOpenOrder(Request $request){
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair_info = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $openOrders = ExchangeOrders::where('status', 0)->where('type','not like', 'convert')->get();
        foreach($openOrders as $order){
            $token_from = Tokens::find($order->unit_from)->token_symbol;
            $token_to = Tokens::find($order->unit_to)->token_symbol;
            if($order->type == 'buy')   {
                $token_pair = $token_from."_".$token_to;
                $cur_price = $response_pair_info[$token_pair]["last"];
                if(floatval($order->limit_price) >= floatval($cur_price)){
                    $wallet_id = Wallets::where('user_id', $order->user_id)->first()->id;
                    $from_account = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $order->unit_from)->first();
                    $to_account = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $order->unit_to)->first();
                    $from_balance = $from_account?$from_account->amount:0;
                    if(floatval($from_balance) >= floatval($order->amount)){
                        $from_account->amount = floatval($from_balance)-floatval($order->amount);
                        $from_account->save();
                        if($to_account){
                            $to_account->amount = floatval($to_account->amount)+floatval($order->total);
                            $to_account->save();
                        }
                        else{
                            $to_account = new Accounts();
                            $to_account->wallet_id = $wallet_id;
                            $to_account->account_type = "exchange";
                            $to_account->token_id = $order->unit_to;
                            $to_account->amount = $order->total;
                            $to_account->save();
                        }
                        $order->status = 1;
                        $order->save();
                    }
                }
            }
            if($order->type == 'sell')   {
                $token_pair = $token_to."_".$token_from;
                $cur_price = $response_pair_info[$token_pair]["last"];
                if($floatval($order->limit_price) <= floatval($cur_price)){
                    $wallet_id = Wallets::where('user_id', $order->user_id)->first()->id;
                    $from_account = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $order->unit_from)->first();
                    $to_account = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $order->unit_to)->first();
                    $from_balance = $from_account?$from_account->amount:0;
                    if(floatval($from_balance) >= floatval($order->total)){
                        $from_account->amount = floatval($from_balance)-floatval($order->total);
                        $from_account->save();
                        if($to_account) {
                            $to_account->amount = floatval($to_account->amount)+floatval($order->amount);
                            $to_account->save();
                        }
                        else{
                            $to_account = new Accounts();
                            $to_account->wallet_id = $wallet_id;
                            $to_account->account_type = "exchange";
                            $to_account->token_id = $order->unit_to;
                            $to_account->amount = $order->amount;
                            $to_account->save();
                        }
                        $order->status = 1;
                        $order->save();
                    }
                }
            }
        }
    }

    public function userOrderCancel(Request $request){
        $order_id = $request->id;

        $order = ExchangeOrders::find($order_id);
        $order->status = null;
        $order->save();

        $id = $request->user_id;
        $toCoin = $request->toCoin;
        $unit_to = Tokens::where('token_symbol',$toCoin)->first()->id;
        $fromCoin = $request->fromCoin;
        $unit_from = Tokens::where('token_symbol',$fromCoin)->first()->id;
        $type = $request->type;

        $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('unit_to',$unit_to)->where('unit_from',$unit_from)->where('type','not like','convert')->where('cleared', '0')->get();
        $result = [];
        foreach($exchangeOrders as $exchangeOrder){
            $data = [];
            $date = new DateTime($exchangeOrder->created_at);
            $data["datetime"] = $date->format('m-d H:i:s');
            $data["price"] = $exchangeOrder->limit_price;
            $data["amount"] = $exchangeOrder->amount;
            $data["total"] = $exchangeOrder->total;
            $data["status"] = $exchangeOrder->status;
            $data["type"] = $exchangeOrder->pay_type;
            $data["side"] = $exchangeOrder->type;
            $data["fee"] = $exchangeOrder->fee;
            $data["filled"] = $exchangeOrder->filled?$exchangeOrder->filled:0;
            $token1 = Tokens::find($exchangeOrder->unit_from)->token_symbol;
            $token2 = Tokens::find($exchangeOrder->unit_to)->token_symbol;
            $pair = ($exchangeOrder->type=='buy'?$token2:$token1)."/".($exchangeOrder->type=='buy'?$token1:$token2);
            $data["pair"] = $pair;
            $data["id"] = $exchangeOrder->id;
            $result[] = $data;
        }

        $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('unit_to',$unit_from)->where('unit_from',$unit_to)->where('type','not like','convert')->where('cleared', '0')->get();
        foreach($exchangeOrders as $exchangeOrder){
            $data = [];
            $date = new DateTime($exchangeOrder->created_at);
            $data["datetime"] = $date->format('m-d H:i:s');
            $data["price"] = $exchangeOrder->limit_price;
            $data["amount"] = $exchangeOrder->amount;
            $data["total"] = $exchangeOrder->total;
            $data["status"] = $exchangeOrder->status;
            $data["type"] = $exchangeOrder->pay_type;
            $data["side"] = $exchangeOrder->type;
            $data["fee"] = $exchangeOrder->fee;
            $data["filled"] = $exchangeOrder->filled?$exchangeOrder->filled:0;
            $token1 = Tokens::find($exchangeOrder->unit_from)->token_symbol;
            $token2 = Tokens::find($exchangeOrder->unit_to)->token_symbol;
            $pair = ($exchangeOrder->type=='buy'?$token2:$token1)."/".($exchangeOrder->type=='buy'?$token1:$token2);
            $data["pair"] = $pair;
            $data["id"] = $exchangeOrder->id;
            $result[] = $data;
        }
        return response()->json($result);
    }

    public function userOrderClear(Request $request){
        $order_id = $request->id;

        $order = ExchangeOrders::find($order_id);
        $order->cleared = 1;
        $order->status = null;
        $order->save();

        $id = $request->user_id;
        $toCoin = $request->toCoin;
        $unit_to = Tokens::where('token_symbol',$toCoin)->first()->id;
        $fromCoin = $request->fromCoin;
        $unit_from = Tokens::where('token_symbol',$fromCoin)->first()->id;
        $type = $request->type;

        $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('unit_to',$unit_to)->where('unit_from',$unit_from)->where('type','not like','convert')->where('cleared', '0')->get();
        $result = [];
        foreach($exchangeOrders as $exchangeOrder){
            $data = [];
            $date = new DateTime($exchangeOrder->created_at);
            $data["datetime"] = $date->format('m-d H:i:s');
            $data["price"] = $exchangeOrder->limit_price;
            $data["amount"] = $exchangeOrder->amount;
            $data["total"] = $exchangeOrder->total;
            $data["status"] = $exchangeOrder->status;
            $data["type"] = $exchangeOrder->pay_type;
            $data["side"] = $exchangeOrder->type;
            $data["fee"] = $exchangeOrder->fee;
            $data["filled"] = $exchangeOrder->filled?$exchangeOrder->filled:0;
            $token1 = Tokens::find($exchangeOrder->unit_from)->token_symbol;
            $token2 = Tokens::find($exchangeOrder->unit_to)->token_symbol;
            $pair = ($exchangeOrder->type=='buy'?$token2:$token1)."/".($exchangeOrder->type=='buy'?$token1:$token2);
            $data["pair"] = $pair;
            $data["id"] = $exchangeOrder->id;
            $result[] = $data;
        }

        $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('unit_to',$unit_from)->where('unit_from',$unit_to)->where('type','not like','convert')->where('cleared', '0')->get();
        foreach($exchangeOrders as $exchangeOrder){
            $data = [];
            $date = new DateTime($exchangeOrder->created_at);
            $data["datetime"] = $date->format('m-d H:i:s');
            $data["price"] = $exchangeOrder->limit_price;
            $data["amount"] = $exchangeOrder->amount;
            $data["total"] = $exchangeOrder->total;
            $data["status"] = $exchangeOrder->status;
            $data["type"] = $exchangeOrder->pay_type;
            $data["side"] = $exchangeOrder->type;
            $data["fee"] = $exchangeOrder->fee;
            $data["filled"] = $exchangeOrder->filled?$exchangeOrder->filled:0;
            $token1 = Tokens::find($exchangeOrder->unit_from)->token_symbol;
            $token2 = Tokens::find($exchangeOrder->unit_to)->token_symbol;
            $pair = ($exchangeOrder->type=='buy'?$token2:$token1)."/".($exchangeOrder->type=='buy'?$token1:$token2);
            $data["pair"] = $pair;
            $data["id"] = $exchangeOrder->id;
            $result[] = $data;
        }
        return response()->json($result);
    }

    public function userTradeList(Request $request){
        $id = $request->user_id;
        $toCoin = $request->toCoin;
        $unit_to = Tokens::where('token_symbol',$toCoin)->first()->id;
        $fromCoin = $request->fromCoin;
        $unit_from = Tokens::where('token_symbol',$fromCoin)->first()->id;
        $type = $request->type;

        if($type == 0) $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('status',1)->where('unit_to',$unit_to)->where('unit_from',$unit_from)->where('type','buy')->orderBy('created_at', 'DESC')->get();
        else $exchangeOrders = ExchangeOrders::where('user_id',$id)->where('status',0)->where('unit_to',$unit_to)->where('unit_from',$unit_from)->where('type','sell')->orderBy('created_at', 'DESC')->get();
        $result = [];
        foreach($exchangeOrders as $exchangeOrder){
            $data = [];
            $data["price"] = $exchangeOrder->limit_price;
            $data["amount"] = $exchangeOrder->amount;
            $data["total"] = $exchangeOrder->total;
            $data["side"] = $exchangeOrder->type;
            $token1 = Tokens::find($exchangeOrder->unit_from)->token_symbol;
            $token2 = Tokens::find($exchangeOrder->unit_to)->token_symbol;
            $pair = ($exchangeOrder->type=='buy'?$token2:$token1)."/".($exchangeOrder->type=='buy'?$token1:$token2);
            $data["pair"] = $pair;
            $result[] = $data;
        }
        return response()->json($result);
    }

    public function order30balance(Request $request){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=returnTicker',
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

        $response_pair_info = json_decode(curl_exec($curl), true);

        curl_close($curl);

        $id = $request->user_id;
        $exchange_orders = ExchangeOrders::where('user_id', $id)->where('type', 'not like', 'convert')->where('status', 1)->get();
        $total = 0;
        foreach($exchange_orders as $order){
            if($order->type == 'buy'){
                $amount_token = $order->total;
                $token = Tokens::find($order->unit_to)->token_symbol;
                $token_other = Tokens::find($order->unit_from)->token_symbol;
            }
            if($order->type == 'sell'){
                $amount_token = $order->total;
                $token = Tokens::find($order->unit_from)->token_symbol;
                $token_other = Tokens::find($order->unit_to)->token_symbol;
            }
             
            if($token == 'USDT') $amount= floatval($amount_token);
            else {
                $pair = 'USDT_'.$token;
                if(isset($response_pair_info[$pair]))
                    $rate = floatval($response_pair_info[$pair]["last"]);
                else{
                    $pair_temp = 'USDT_'.$token_other;
                    $pair_cur = $token_other."_".$token;
                    $rate = floatval($response_pair_info[$pair_temp]['last']) * floatval($response_pair_info[$pair_cur]['last']);
                }
                $amount = floatval($amount_token) * floatval($rate);
            }

            $total += $amount;
        }

        if($total > 10000000)       $fee = 0.1;
        elseif($total > 1000000)    $fee = 0.15;
        elseif($total > 50000)      $fee = 0.24;
        else                        $fee = 0.25;

        $results = array(
            "volume" => $total,
            "fee" => $fee
        );

        return response()->json($results);
    }
}
