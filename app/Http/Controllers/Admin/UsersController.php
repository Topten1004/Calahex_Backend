<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Profile;
use App\Models\Users;
use App\Models\Wallets;
use App\Models\Tokens;
use App\Models\ExchangeOrders;
use App\Models\Payorders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $you = auth()->user();
        $users = User::all();
        return view('admin.users.usersList', compact('users', 'you'));
    }


    public function search(Request $request){
        $searchText = $request->searchText;
        $you = auth()->user();
        $users = User::where('email','LIKE', "%{$searchText}%")->get();
        return view('admin.users.usersList', compact('users', 'you','searchText'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email'             => 'required|min:1|max:64',
            'password'          => 'required',
            'menuroles'         => 'required',
            'status'            => 'required'
        ]);
        $user = new User([
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'menuroles' => $request->menuroles,
            'status'   => $request->status,
        ]);
        $user->save();
        $request->session()->flash('message', 'Successfully created user');
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function cmp($a, $b){
    //     return strcmp($a->datetime, $b->datetime);
    // }

    public function show($id)
    {
        $user = User::find($id);
        $wallet = Wallets::where('user_id',$id)->first();
        $btc_array = Accounts::where("wallet_id", $wallet->id)->where('account_type','exchange')->where('token_id',1)->first();
        if(!isset($btc_array)) $btc = 0;
        else $btc = $btc_array->amount;

        $eth_array = Accounts::where("wallet_id", $wallet->id)->where('account_type','exchange')->where('token_id',2)->first();
        if(!isset($eth_array)) $eth = 0;
        else $eth = $eth_array->amount;

        $usdt_array = Accounts::where("wallet_id", $wallet->id)->where('account_type','exchange')->where('token_id',3)->first();
        if(!isset($usdt_array)) $usdt = 0;
        else $usdt = $usdt_array->amount;

        $tokens = Tokens::where('for_cefi', 1)->where('status', 2)->get();
        $accounts = Accounts::where('wallet_id', $wallet->id)->get();

        $logData = array();

        $exchange_order = ExchangeOrders::where('user_id', $id)->get();
        $pay_order = Payorders::where('user_id', $id)->get();

        $i = 1;
        foreach($exchange_order as $order){
            $status = "";
            if($order->type == 'convert')   $status = "done";
            else{
                if($status=='1')          $status = "traded";
                if($status=='0')          $status = "open";
                if($status==null)       $status = "closed";
            }
            $token_from = Tokens::where('id', $order->unit_from)->first()->token_symbol;
            $token_to = Tokens::where('id', $order->unit_to)->first()->token_symbol;

            $amount = $order->amount." ".$token_from;
            if($order->type == 'sell')  $amount = $order->total." " .$token_from;
            $total = $order->total." ".$token_to;
            if($order->type == 'sell')   $total = $order->amount." ".$token_to;
            $logData[] = array(
                "index" => $i,
                "datetime" => $order->trade_at?$order->trade_at:$order->created_at,
                "token" => $token_from."/".$token_to,
                "price" => $order->limit_price,
                "amount" => $amount,
                "amount_left" => ($order->amount_left?$order->amount_left:"0")." ".$token_from,
                "amount_total" => $total,
                "account" => "exchange",
                "type" => $order->type,
                "detail" => "",
                "address" => "",
                "payment_id" => "",
                "status" => $status
            );
            $i ++;
        }

        foreach($pay_order as $order){
            $logData[] = array(
                "index" => $i,
                "datetime" => $order->created_at,
                "token" => $order->unit,
                "price" => "",
                "amount" => $order->amount." ".$order->unit,
                "amount_left" => ($order->amount_left?$order->amount_left:"0")." ".$order->unit,
                "amount_total" => "",
                "account" => "exchange",
                "type" => $order->payment_type,
                "detail" => $order->reference,
                "address" => $order->address,
                "payment_id" => $order->payment_id,
                "status" => $order->status
            );
            $i ++;
        }
        usort($logData, function ($a, $b){
            return strcmp($b['datetime'], $a['datetime']);
        });
        $logData = json_encode($logData);

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

        $wallet = Wallets::where('user_id',$id)->first();
        $wallet_id = $wallet->id;
        $accounts = Accounts::where('wallet_id',$wallet_id)->orderBy('token_id', 'asc')->get();
        $total_amount = 0.00;
        foreach($accounts as $account){
            $pair ='USDT_'.$account->token->token_symbol;
            if($account->token->token_symbol == 'USDT') $value= floatval($account->amount);
            else $value= floatval($account->amount) * floatval($response_pair_info[$pair]["last"]);
            $total_amount +=$value;
        }
        $total_amount = number_format(floatval($total_amount), 8, '.', ',');

        $tokens = Tokens::where('for_cefi', 1)->where('status', 2)->get();
        $balances = [];
        foreach($tokens as $token){
            $exchange = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $token->id)->first();
            $exchange_balance = ($exchange)?$exchange->amount:0;
            $margin = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'margin')->where('token_id', $token->id)->first();
            $margin_balance = ($margin)?$margin->amount:0;
            $futures = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'futures')->where('token_id', $token->id)->first();
            $futures_balance = ($futures)?$futures->amount:0;
            $savings = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'savings')->where('token_id', $token->id)->first();
            $savings_balance = ($savings)?$savings->amount:0;
            $pool = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'pool')->where('token_id', $token->id)->first();
            $pool_balance = ($pool)?$pool->amount:0;
            $token_symbol = $token->token_symbol;
            $balances[] = array(
                "token_symbol" => $token_symbol,
                "exchange_balance" => $exchange_balance,
                "margin_balance" => $margin_balance,
                "futures_balance" => $futures_balance,
                "savings_balance" => $savings_balance,
                "pool_balance" => $pool_balance,
            );
        }
        $balances = json_encode($balances);

        return view('admin.users.userShow', compact( 'user','wallet','btc','eth','usdt','tokens','accounts','logData', 'total_amount', 'balances'));
    }

    public function setBalance(Request $request){
        $user_id = $request->user_id;
        $token_id = $request->token;
        $token_symbol = Tokens::find($token_id)->token_symbol;
        $type = $request->type;
        $amount = $request->amount;
        $wallet_id = Wallets::where('user_id', $user_id)->first()->id;
        $account = Accounts::where('account_type', $type)->where('token_id', $token_id)->where('wallet_id', $wallet_id)->first();
        if($account){
            if($account->amount == $amount){
                return "No Change";
            }
            $account->amount = $amount;
            $account->save();
        }
        else{
            $account = new Accounts();
            $account->wallet_id = $wallet_id;
            $account->account_type = $type;
            $account->token_id = $token_id;
            $account->amount = $amount;
            $account->save();
        }
        $payorder = new Payorders();
        $payorder->user_id = $user_id;
        $payorder->payment_id = "";
        $payorder->reference = "Normal Issue";
        $payorder->unit = $token_symbol;
        $payorder->amount = $amount;
        $payorder->address = "";
        $payorder->amount_left = 0;
        $payorder->payment_type = "error";
        $payorder->status = "done";
        $payorder->save();
    }

    public function manage(Request $request){
        $validatedData = $request->validate([
            'btc'             => 'required|numeric',
            'eth'          => 'required|numeric',
            'usdt'         => 'required|numeric',
        ]);


        $id = $request->id;
        $user = User::find($id);
        $wallet_id = Wallets::where('user_id',$id)->first()->id;

        $wallet = Wallets::find($wallet_id);

        $btc = $request->btc;

        $account_btc = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',1)->first();
        if(isset($account_btc)){
            $account_btc->amount = floatVal($account_btc->amount)+floatVal($btc);
            $account_btc->save();
        } else {
            $account_btc = New Accounts();
            $account_btc->amount = $btc;
            $account_btc->wallet_id = $wallet_id;
            $account_btc->token_id = 1;
            $account_btc->account_type = 'exchange';
            $account_btc->save();
        }
        if($btc != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "BTC";
            $payorder->amount = $btc;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $eth = $request->eth;
        $account_eth = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',2)->first();

        if(isset($account_eth)){
            $account_eth->amount = floatVal($account_eth->amount)+floatVal($eth);
            $account_eth->save();
        } else {
            $account_eth = New Accounts();
            $account_eth->amount = $eth;
            $account_eth->wallet_id = $wallet_id;
            $account_eth->token_id = 2;
            $account_eth->account_type = 'exchange';
            $account_eth->save();
        }
        if($eth != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "ETH";
            $payorder->amount = $eth;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $usdt = $request->usdt;
        $account_usdt = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',3)->first();

        if(isset($account_usdt)){
            $account_usdt->amount = floatVal($account_usdt->amount)+floatVal($usdt);
            $account_usdt->save();
        } else {
            $account_usdt = New Accounts();
            $account_usdt->amount = $usdt;
            $account_usdt->wallet_id = $wallet_id;
            $account_usdt->token_id = 3;
            $account_usdt->account_type = 'exchange';
            $account_usdt->save();
        }
        if($usdt != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "USDT";
            $payorder->amount = $usdt;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $sxp = $request->sxp;
        $account_sxp = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',48)->first();

        if(isset($account_sxp)){
            $account_sxp->amount = floatVal($account_sxp->amount)+floatVal($sxp);
            $account_sxp->save();
        } else {
            $account_sxp = New Accounts();
            $account_sxp->amount = $sxp;
            $account_sxp->wallet_id = $wallet_id;
            $account_sxp->token_id = 48;
            $account_sxp->account_type = 'exchange';
            $account_sxp->save();
        }
        if($sxp != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "SXP";
            $payorder->amount = $sxp;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $rep = $request->rep;
        $account_rep = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',43)->first();

        if(isset($account_rep)){
            $account_rep->amount = floatVal($account_rep->amount)+floatVal($rep);
            $account_rep->save();
        } else {
            $account_rep = New Accounts();
            $account_rep->amount = $rep;
            $account_rep->wallet_id = $wallet_id;
            $account_rep->token_id = 43;
            $account_rep->account_type = 'exchange';
            $account_rep->save();
        }
        if($rep != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "REPV2";
            $payorder->amount = $rep;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $yfi = $request->yfi;
        $account_yfi = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',30)->first();

        if(isset($account_yfi)){
            $account_yfi->amount = floatVal($account_yfi->amount)+floatVal($yfi);
            $account_yfi->save();
        } else {
            $account_yfi = New Accounts();
            $account_yfi->amount = $yfi;
            $account_yfi->wallet_id = $wallet_id;
            $account_yfi->token_id = 30;
            $account_yfi->account_type = 'exchange';
            $account_yfi->save();
        }
        if($yfi != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "YFI";
            $payorder->amount = $yfi;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $uni = $request->uni;
        $account_uni = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',26)->first();

        if(isset($account_uni)){
            $account_uni->amount = floatVal($account_uni->amount)+floatVal($uni);
            $account_uni->save();
        } else {
            $account_uni = New Accounts();
            $account_uni->amount = $uni;
            $account_uni->wallet_id = $wallet_id;
            $account_uni->token_id = 26;
            $account_uni->account_type = 'exchange';
            $account_uni->save();
        }
        if($uni != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "UNI";
            $payorder->amount = $uni;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $link = $request->link;
        $account_link = Accounts::where('account_type','exchange')->where('wallet_id',$wallet_id)->where('token_id',7)->first();

        if(isset($account_link)){
            $account_link->amount = floatVal($account_link->amount)+floatVal($link);
            $account_link->save();
        } else {
            $account_link = New Accounts();
            $account_link->amount = $link;
            $account_link->wallet_id = $wallet_id;
            $account_link->token_id = 7;
            $account_link->account_type = 'exchange';
            $account_link->save();
        }
        if($link != '0'){
            $payorder = new Payorders();
            $payorder->user_id = $user->id;
            $payorder->payment_id = "";
            $payorder->reference = "Blockchain payment";
            $payorder->unit = "LINK";
            $payorder->amount = $link;
            $payorder->address = "";
            $payorder->amount_left = 0;
            $payorder->payment_type = "error";
            $payorder->status = "done";
            $payorder->save();
        }

        $margin_paid = $request->margin_paid;

        $margin_activated = $request->margin_activated;

        if(isset($margin_paid)){
            if(!isset($wallet->margin_paid_at)) $wallet->margin_paid_at = Carbon::now();
        } else $wallet->margin_paid_at = null;

        if($margin_activated == 1){
            if(!isset($wallet->margin_activated_at)) $wallet->margin_activated_at = Carbon::now();
        } else $wallet->margin_activated_at = null;

        $margin_verified = $request->margin_verified;
        if(isset($margin_verified)){
            if(!isset($user->video_verified_at)) $user->video_verified_at = Carbon::now();
        } else {
            $user->video_verified_at = null;
        }

        $pool_paid = $request->pool_paid;
        $pool_activated = $request->pool_activated;
        if(isset($pool_paid)){
            if(!isset($wallet->pool_paid_at)) $wallet->pool_paid_at = Carbon::now();
        } else $wallet->pool_paid_at = null;

        if($pool_activated == 1){
            if(!isset($wallet->pool_activated_at)) $wallet->pool_activated_at = Carbon::now();
        } else $wallet->pool_activated_at = null;

        $pool_verified = $request->pool_verified;
        if(isset($pool_verified)){
            if(!isset($user->auth_verified_at)) $user->auth_verified_at = Carbon::now();
        } else {
            $user->auth_verified_at = null;
        }

        $saving_paid = $request->saving_paid;
        if(isset($saving_paid)){
            if(!isset($wallet->saving_paid_at)) $wallet->saving_paid_at = Carbon::now();
        } else $wallet->saving_paid_at = null;
        $saving_activated = $request->saving_activated;
        if($saving_activated == 1){
            if(!isset($wallet->savings_activated_at)) $wallet->savings_activated_at = Carbon::now();
        } else $wallet->savings_activated_at = null;
        $saving_verified = $request->saving_verified;
        if(isset($saving_verified)){
            if(!isset($user->phone_verified_at)) $user->phone_verified_at = Carbon::now();
        } else {
            $user->phone_verified_at = null;
        }


        $future_activated = $request->future_activated;

        if($future_activated == 1){
            if(!isset($wallet->futures_activated_at)) $wallet->futures_activated_at = Carbon::now();
        } else $wallet->futures_activated_at = null;
        $wallet->save();
        $user->save();
        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $profile = Profile::where('user_id',$id)->first();

        return view('admin.users.userEditForm', compact('user','profile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'email'             => 'required|min:1|max:64',
            'password'          => 'required',
            'status'            => 'required'
        ]);

        $user = User::find($id);
        $user->email      = $request->input('email');
        $user->password   =bcrypt($request->password);
        $user->status     = $request->status;
        if($request->checkedB == true) {
            $user->auth_verified_at = Carbon::now();
        } else {
            $user->auth_verified_at = null;
        }
        $user->save();

        $profile = Profile::where('user_id',$id)->first();
        if(isset($profile)){
            $profile->firstname = $request->first_name;
            $profile->lastname = $request->last_name;
            $profile->country = $request->country;
            $profile->city = $request->city;
            $profile->street = $request->street;
            $profile->postal_code = $request->postal_code;
            $profile->phone_number = $request->phone;
            $profile->birthday = $request->birthday;
            $profile->ip_address = $request->ip_address;
            $profile->language = $request->language;

            $profile->mother_name = $request->mother_name;
            $profile->father_name = $request->father_name;
            $profile->nick_name = $request->nick_name;
            $profile->hobby = $request->hobby;
            $profile->best_friend = $request->best_friend;

            $profile->save();
        }
        else {
            $profile = New Profile();
            $profile->user_id = $id;
            $profile->firstname = $request->first_name;
            $profile->lastname = $request->last_name;
            $profile->country = $request->country;
            $profile->city = $request->city;
            $profile->street = $request->street;
            $profile->postal_code = $request->postal_code;
            $profile->phone_number = $request->phone;
            $profile->birthday = $request->birthday;
            $profile->ip_address = $request->ip_address;
            $profile->language = $request->language;

            $profile->mother_name = $request->mother_name;
            $profile->father_name = $request->father_name;
            $profile->nick_name = $request->nick_name;
            $profile->hobby = $request->hobby;
            $profile->best_friend = $request->best_friend;

            $profile->save();
        }


        $request->session()->flash('message', 'Successfully updated user');
        return redirect()->route('users.index');
    }

    /**
     * Block the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function block($id)
    {
        $user = User::find($id);
        $user->status = 1;
        $user->save();
        return redirect()->route('users.index');
    }

    /**
     * Unblock the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unblock($id)
    {
        $user = User::find($id);
        $user->status = 0;
        $user->save();
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if($user){
            $user->delete();
        }
        return redirect()->route('users.index');
    }
}
