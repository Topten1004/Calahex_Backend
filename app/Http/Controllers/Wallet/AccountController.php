<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Tokens;
use App\Models\User;
use App\Models\Wallets;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    //
    public function accountInfo(Request $request){

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

        $accounts = Accounts::where('wallet_id',$wallet_id)->orderBy('token_id', 'asc')->get();



        $total_amount = 0.00;
        $exchange_amount = 0.00;
        $margin_amount = 0.00;
        $futures_amount = 0.00;
        $savings_amount = 0.00;
        $pool_amount = 0.00;
        $accountData = array();
        $accountTokenAmount = [];
        $tmp = [];
        foreach($accounts as $account){
            $pair ='USDT_'.$account->token->token_symbol;
            if($account->token->token_symbol == 'USDT') $value= floatval($account->amount);
            else {                
                if(isset($response_pair_info[$pair]))
                    $rate = floatval($response_pair_info[$pair]["last"]);
                else{
                    $pair_temp = 'USDT_'.explode(",", $account->token->token_pair_type)[0];
                    $pair_cur = explode(",", $account->token->token_pair_type)[0]."_".$account->token->token_symbol;
                    $rate = floatval($response_pair_info[$pair_temp]['last']) * floatval($response_pair_info[$pair_cur]['last']);
                }
                $value= floatval($account->amount) * floatval($rate);
            }
            if($account->account_type == "exchange") $exchange_amount += $value;
            else if($account->account_type == "margin") $margin_amount +=$value;
            else if($account->account_type == "futures") $futures_amount +=$value;
            else if($account->account_type == "savings") $savings_amount +=$value;
            else if($account->account_type == "pool") $pool_amount +=$value;


            $total_amount +=$value;
            $accountData[$account->token->token_symbol][$account->account_type] = number_format(floatval($account->amount),8,'.',',');
            $accountData[$account->token->token_symbol][$account->account_type] = $account->amount;
            if(!isset($tmp[$account->token->token_symbol])) {
                $accountData[$account->token->token_symbol]['coin'] =$account->token->token_symbol;
                $tmp[$account->token->token_symbol]= floatval($account->amount);
                $accountTokenAmount[$account->token->token_symbol] = $tmp[$account->token->token_symbol];
            }
            else {
                $tmp[$account->token->token_symbol] += floatval($account->amount);
                $accountTokenAmount[$account->token->token_symbol] = $tmp[$account->token->token_symbol];
            }

        }

        $coinArray = [];
        $coinSymbols = Tokens::all();
        foreach($coinSymbols as $symbol){
            $coinArray[]= $symbol->token_symbol;
        }
        $data = [];

        $data["token_info"] = $coinArray;

        $data["total_amount"]=number_format(floatval($total_amount), 8, '.', ',');
        $data["exchange_amount"] = number_format(floatval($exchange_amount), 8, '.', ',');
        $data["margin_amount"] = number_format(floatval($margin_amount), 8, '.', ',');
        $data["futures_amount"] = number_format(floatval($futures_amount), 8, '.', ',');
        $data["savings_amount"] = number_format(floatval($savings_amount), 8, '.', ',');
        $data["pool_amount"] = number_format(floatval($pool_amount), 8, '.', ',');

        $user = User::find($id);

        if(isset($user->phone_verified_at) && $user->phone_verified_at != null) $data["futures_state"] = true;
        else $data["futures_state"] =false;

        if(isset($user->video_verified_at) && $user->video_verified_at != null) $data["margin_state"] = true;
        else $data["margin_state"] =false;

        if(isset($user->auth_verified_at) && $user->auth_verified_at != null) $data["pool_state"] = true;
        else $data["pool_state"] =false;

        if(isset($user->phone_verified_at) && $user->phone_verified_at != null) $data["savings_state"] = true;
        else $data["savings_state"] =false;
        $data["detail_data"] = $accountData;
        $data["token_amount"] = $accountTokenAmount;


        $result[] = $data;

        return response()->json($result);
    }

    public function activateState(Request $request){
        $id = $request->user_id;
        $type = $request->type;
        $state = $request->state;

        $wallet = Wallets::where('user_id',$id)->first();

        if($type == "margin"){
            if($state == true) $wallet->margin_activated_at = date('Y-m-d H:i:s');
            else $wallet->margin_activated_at = null;
        }
        if($type == "futures"){
            if($state == true) $wallet->futures_activated_at = date('Y-m-d H:i:s');
            else $wallet->futures_activated_at = null;
        }
        if($type == "pool"){
            if($state == true) $wallet->pool_activated_at = date('Y-m-d H:i:s');
            else $wallet->pool_activated_at = null;
        }
        if($type == "savings"){
            if($state == true) $wallet->savings_activated_at = date('Y-m-d H:i:s');
            else $wallet->savings_activated_at = null;
        }
        $wallet->save();

        if(isset($wallet->futures_activated_at) && $wallet->futures_activated_at != null) $data["futures_state"] = true;
        else $data["futures_state"] =false;

        if(isset($wallet->margin_activated_at) && $wallet->margin_activated_at != null) $data["margin_state"] = true;
        else $data["margin_state"] =false;

        if(isset($wallet->pool_activated_at) && $wallet->pool_activated_at != null) $data["pool_state"] = true;
        else $data["pool_state"] =false;

        if(isset($wallet->savings_activated_at) && $wallet->savings_activated_at != null) $data["savings_state"] = true;
        else $data["savings_state"] =false;
        $result[] = $data;
        return response()->json($result);
    }

    public function amountTransfer(Request $request) {
        $id = $request->user_id;
        $amount = $request->amount;
        $coin = $request->coin;
        $fromAccount = $request->fromAccount;
        $toAccount = $request->toAccount;
        $wallet_id = Wallets::where('user_id',$id)->first()->id;
        $token_id = Tokens::where('token_symbol',$coin)->first()->id;
        $account = Accounts::where('wallet_id',$wallet_id)->where('account_type', $fromAccount)->where('token_id',$token_id)->first();
        $account->amount = $account->amount - $amount;
        $account->save();


        $account = Accounts::where('wallet_id',$wallet_id)->where('account_type', $toAccount)->where('token_id',$token_id)->first();

            if(isset($account)){
                $account->amount = $account->amount + $amount;
                $account->save();
            }else {
                $account = New Accounts();
                $account->wallet_id = $wallet_id;
                $account->token_id = $token_id;
                $account->account_type = $toAccount;
                $account->amount = $amount;
                $account->save();
            }

        return response()->json([
            'message' => 'Successfully updated account!'
        ], 201);
    }

    public function verifyInfo(Request $request){
        $user = User::find($request->user_id);
        $data = [];
        $data["phone_verified_at"] = $user->phone_verified_at;
        $data["auth_verified_at"] = $user->auth_verified_at;
        $data["video_verified_at"] = $user->video_verified_at;

        $wallet = Wallets::where('user_id',$request->user_id)->first();
        $data["futures_activated_at"] = $wallet->futures_activated_at;
        $data["savings_activated_at"] = $wallet->savings_activated_at;
        $data["pool_activated_at"] = $wallet->pool_activated_at;
        $data["margin_activated_at"] = $wallet->margin_activated_at;

        $data["saving_paid_at"] = $wallet->saving_paid_at;
        $data["pool_paid_at"] = $wallet->pool_paid_at;
        $data["margin_paid_at"] = $wallet->margin_paid_at;

        return response()->json($data);
    }
}
