<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Payorders;
use App\Models\Tokens;
use App\Models\ExchangeOrders;
use App\Models\Wallets;
use App\Models\User;
use Illuminate\Http\Request;
use function MongoDB\BSON\toJSON;
use App\Mail\Mailer;

class ExchangeController extends Controller
{
    //
    public function exchangeInfo(Request $request){
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

        $wallet = Wallets::where('user_id',$id)->first();
        $wallet_id = $wallet->id;

        $accounts = Accounts::where('wallet_id',$wallet_id)->where('account_type','exchange')->orderBy('token_id', 'asc')->get();
        $result = [];
        $total = 0.00;
        foreach($accounts as $account) {
            if($account->token->token_symbol == 'USDT') $value= floatval($account->amount);
            else {
                $pair = 'USDT_'.$account->token->token_symbol;
                if(isset($response_pair_info[$pair]))
                    $rate = floatval($response_pair_info[$pair]["last"]);
                else{
                    $pair_temp = 'USDT_'.explode(",", $account->token->token_pair_type)[0];
                    $pair_cur = explode(",", $account->token->token_pair_type)[0]."_".$account->token->token_symbol;
                    $rate = floatval($response_pair_info[$pair_temp]['last']) * floatval($response_pair_info[$pair_cur]['last']);
                }
                $value= floatval($account->amount) * floatval($rate);
            }
            $total+=$value;
            $data = [];

            $data["coin"] = $account->token->token_symbol;
            $data["available"] = $account->amount;
            $data["locked"] = 0.000000;
            $data["locked_deposit"] = 0.000000;
            $data["locked_withdraw"] = 0.000000;
            $result[$account->token_id] = $data;
        }

        $orders = ExchangeOrders::where('user_id', $id)->where('status', '0')->get();
        foreach($orders as $order){

            if(isset($result[$order->unit_from])) {;
                $result[$order->unit_from]["locked"]= floatval($result[$order->unit_from]["locked"])-($order->type=='buy'?floatval($order->amount):floatval($order->total));
            }
            else{
                $data = [];
                $token_symbol = Tokens::find($order->unit_from)->token_symbol;
                $data["coin"] = $token_symbol;
                $data["available"] = 0.00000000;
                $data["locked"] = $order->type=='buy'?floatval($order->amount):floatval($order->total);
                $data["locked_deposit"] = 0.000000;
                $data["locked_withdraw"] = 0.000000;
                $result[$order->unit_from] = $data;
            }

            if(isset($result[$order->unit_to])) {
                $result[$order->unit_to]["locked"] = floatval($result[$order->unit_to]["locked"])+($order->type=='buy'?floatval($order->total):floatval($order->amount));
            }
            else{
                $data = [];
                $token_symbol = Tokens::find($order->unit_to)->token_symbol;
                $data["coin"] = $token_symbol;
                $data["available"] = 0.00000000;
                $data["locked"] = $order->type=='buy'?floatval($order->total):floatval($order->amount);
                $data["locked_deposit"] = 0.000000;
                $data["locked_withdraw"] = 0.000000;
                $result[$order->unit_to] = $data;
            }
        }

        $payorders = Payorders::where('user_id', $id)->where('payment_type', 'not like', 'error')->where('reference', 'crypto')->where('status', 'requesting')->get();
        foreach($payorders as $order){
            $token = Tokens::where('token_symbol', $order->unit)->first();

            if($token){
                $token_id = $token->id;
                if(isset($result[$token_id])){
                    $result[$token_id]["locked_deposit"] = floatval($result[$token_id]["locked_deposit"]) + floatval($order->payment_type=='deposit'?$order->amount:0.000000);
                    $result[$token_id]["locked_withdraw"] = floatval($result[$token_id]["locked_withdraw"]) + floatval($order->payment_type=='withdraw'?$order->amount:0.000000);
                }
                else{
                    $data = [];
                    $data["coin"] = $order->unit;
                    $data["available"] = 0.000000;
                    $data["locked"] = 0.000000;
                    $data["locked_deposit"] = $order->payment_type=='deposit'?$order->amount:0.000000;
                    $data["locked_withdraw"] = $order->payment_type=='withdraw'?$order->amount:0.000000;
                    $result[$token_id] = $data;
                }
            }
        }

        $tokens = Tokens::where('for_cefi',1)->where('status',2)->get();
        $ans = [];
        foreach($tokens as $token) {
            if (isset($result[$token->id])) {
                $data = $result[$token->id];
                $data["logo"] = $token->token_logo;

                if($token->token_symbol == 'USDT')  $data["rate"] = 1;
                else{
                    $pair = "USDT_".$token->token_symbol;
                    if(isset($response_pair_info[$pair]))
                        $data["rate"] = floatval($response_pair_info[$pair]["last"]);
                    else{
                        $pair_temp = 'USDT_BTC';
                        $pair_cur = 'BTC_'.$token->token_symbol;
                        if(isset($response_pair_info[$pair_cur]))
                            $data["rate"] = floatval($response_pair_info[$pair_temp]['last']) * floatval($response_pair_info[$pair_cur]['last']);
                        else{
                            $pair_temp = 'USDT_ETH';
                            $pair_cur = 'ETH_'.$token->token_symbol;
                            $data["rate"] = floatval($response_pair_info[$pair_temp]['last']) * floatval($response_pair_info[$pair_cur]['last']);
                        }
                    }
                }
            }

            else{
                $data = [];
                $data["coin"] = $token->token_symbol;
                $data["logo"] = $token->token_logo;

                if($token->token_symbol == 'USDT')  $data["rate"] = 1;
                else{
                    $pair = "USDT_".$token->token_symbol;
                    if(isset($response_pair_info[$pair]))
                        $data["rate"] = floatval($response_pair_info[$pair]["last"]);
                    else{
                        $pair_temp = 'USDT_BTC';
                        $pair_cur = 'BTC_'.$token->token_symbol;
                        if(isset($response_pair_info[$pair_cur]))
                            $data["rate"] = floatval($response_pair_info[$pair_temp]['last']) * floatval($response_pair_info[$pair_cur]['last']);
                        else{
                            $pair_temp = 'USDT_ETH';
                            $pair_cur = 'ETH_'.$token->token_symbol;
                            $data["rate"] = floatval($response_pair_info[$pair_temp]['last']) * floatval($response_pair_info[$pair_cur]['last']);
                        }
                    }
                }

                $data["available"] = 0.00000000;
                $data["locked"] = 0.000000;
                $data["locked_deposit"] = 0.000000;
                $data["locked_withdraw"] = 0.000000;
            }

            $ans[] = $data;
        }
        usort($ans, function ($a, $b) {
            $pos_a = $a['available'];
            $pos_b = $b['available'];
            if($a['coin'] == 'BTC' || $a['coin'] == 'ETH' || $a['coin'] == 'USDT') return false;

            if($pos_b - $pos_a > 0.00) return true;
            else return false;
        });

        return response()-> json([
            "amount"=>number_format(floatval($total), 8, '.', ','),
            "amount_BTC"=>floatval($total)/floatval($response_pair_info["USDT_BTC"]["last"]),
            "exchangeInfo" => $ans,
            "pairInfo" => $response_pair_info
            ]);
    }

    public function withdrawConfirm(Request $request){
        $user_id = $request->user_id;
        $key = intval($request->key)/7;
        $email = User::find($user_id)->email;
        try {
            $details = [
                'to' => $email,
                'from' => 'customerservice@calahex.io',
                'subject' => 'Calahex.com Withdrawal Confirmation',
                'title' => 'Calahex.com Withdrawal Confirmation',
                'key' 	=> $key,
                'type' => 'confirmwithdraw'
            ];
            \Mail::to($email)->send(new \App\Mail\Mailer($details));
            if (\Mail::failures()) {
                return response()->json([
                    'status'  => false,
                    'data'    => $details,
                    'message' => 'Not sending mail.. retry again...'
                ]);
            }
            return response()->json([
                'message' => 'Successfully message Sent!'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e
            ], 201);
        }
    }

    public function exchangeConvert(Request $request){
        $id = $request->user_id;
        $toCoin = $request->toCoin;
        $toAmount = $request->toAmount;
        $fromCoin = $request->fromCoin;
        $fromAmount = $request->fromAmount;
        $price = $request->price;

        $wallet = Wallets::where('user_id',$id)->first();
        $wallet_id = $wallet->id;
        $token_id = Tokens::where('token_symbol',$fromCoin)->first()->id;
        $account = Accounts::where('wallet_id',$wallet_id)->where('account_type', 'exchange')->where('token_id',$token_id)->first();

        if($account->amount < $fromAmount)
            return response()->json(["message"=>"No sufficient fund"]);

        $account->amount -= $fromAmount;
        $amount_left = $account->amount;
        $account->save();

        $token_id1 = Tokens::where('token_symbol',$toCoin)->first()->id;
        $account = Accounts::where('wallet_id',$wallet_id)->where('account_type', 'exchange')->where('token_id',$token_id1)->first();
        if(isset($account)){
            $account->amount += $toAmount;
            $account->save();
        }else {
            $account = New Accounts();
            $account->wallet_id = $wallet_id;
            $account->token_id = $token_id1;
            $account->account_type = 'exchange';
            $account->amount = $toAmount;
            $account->save();
        }

        $convert_log = new ExchangeOrders();
        $convert_log->user_id = $id;
        $convert_log->type = 'convert';
        $convert_log->limit_price = $price;
        $convert_log->amount = $fromAmount;
        $convert_log->amount_left = $amount_left;
        $convert_log->total = $toAmount;
        $convert_log->unit_from = $token_id;
        $convert_log->unit_to = $token_id1;
        $convert_log->save();

        return response()->json([
            "message"=>"Successfully"
        ]);
    }

    public function depositHistory(Request $request){
        $payment_id = $request->payment_id;
        $method = $request->method;
        $id = $request->id;
        if(isset($request->payment_id)){
            $payorders = Payorders::where('payment_id', $payment_id)->first();
            $payorders->status = 'cancelled';
            $payorders->save();
        }
        $payorders = Payorders::where('user_id',$id)->where('payment_type','deposit')->where('reference', $method)->where('status', 'not like', 'cancelled')->orderBy('created_at', 'DESC')->get();
        $result = [];
        foreach($payorders as $payorder){
            $data = [];
            $data["id"] = $payorder->id;
            $data["payment_id"] = $payorder->payment_id;
            $data["coin"] = $payorder->unit;
            $data["status"] = $payorder->status;
            $data["amount"] = $payorder->amount;
            $data["date"] = $payorder->created_at->toDateTimeString();
            $data["action"] = "";
            $result[] = $data;
        }
        return response()->json($result);
    }

    public function withdrawHistory(Request $request){
        $id = $request->user_id;
        $coin = $request->coin;
        if(isset($request->id)){
            $payorders = Payorders::find($request->id);
            $payorders->status = 'cancelled';
            $payorders->save();
        }
        $payorders = Payorders::where('user_id',$id)->where('payment_type','withdraw')->where('reference', 'crypto')->where('status', 'not like', 'cancelled')->orderBy('created_at', 'DESC')->get();
        $result = [];
        foreach($payorders as $payorder){
            $data = [];
            $data["payment_id"] = $payorder->id;
            $data["coin"] = $payorder->unit;
            $data["status"] = $payorder->status;
            $data["amount"] = $payorder->amount;
            $data["date"] = $payorder->created_at->toDateTimeString();
            $data["action"] = "";
            $result[] = $data;
        }

        $available = 0;
        $wallet_id = Wallets::where('user_id', $id)->first()->id;
        $token_id = Tokens::where('token_symbol', $coin)->first()->id;
        $account = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $token_id)->first();
        if($account)    $available = $account->amount;
        $orders = Payorders::where('user_id', $id)->where('payment_type','withdraw')->where('reference', 'crypto')->where('status', 'requesting')->get();
        $order_balance = 0;
        foreach($orders as $order){
            if($order->unit == $coin)
                $order_balance = floatval($order_balance) - floatval($order->amount);
        }
        // $total = floatval($available) + floatval($order_balance);
        $balance = [];
        $balance["available"] = $available;
        $balance["order"] = $order_balance;
        // $balance["total"] = $total;

        return response()->json(['history'=>$result, 'balance'=>$balance]);
    }


}
