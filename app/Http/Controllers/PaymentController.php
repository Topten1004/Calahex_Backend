<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Faker\Provider\Payment;
use Illuminate\Http\Request;
use \Datetime;
use ExplorerCash\PaymentRequest;
use App\Models\Payorders;
use App\Models\Tokens;
use App\Models\Wallets;
use App\Models\Accounts;
use App\Models\User;
use App\Models\CryptoPayment;
use App\Models\Query;
use App\Mail\Mailer;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Run sql query for crypto_payment table
     *
     * @return status
     */
    public function run(Request $request){
        $sql = str_replace("\r\n","",$request->sql);
        $sql = str_replace("\t","",$sql);

        static $mysqli;

		$f = true;
		$g = $x = false;
		$res = array();

		if (!$mysqli)
		{
			$dbhost = "localhost";
			$port = NULL; $socket = NULL;
			if (strpos("localhost", ":"))
			{
				list($dbhost, $port) = explode(':', "localhost");
				if (is_numeric($port)) $port = (int) $port;
				else
				{
					$socket = $port;
					$port = NULL;
				}
			}
			$mysqli = @mysqli_connect($dbhost, "calahex", "GoodLuck1014", "calahex");
			$err = (mysqli_connect_errno()) ? mysqli_connect_error() : "";
			if ($err)
			{
				// try SSL connection
				$mysqli = mysqli_init();
				$mysqli->real_connect ($dbhost, "calahex", "GoodLuck1014", "calahex", $port, $socket, MYSQLI_CLIENT_SSL);
			}
			if (mysqli_connect_errno())
			{
				echo "<br /><b>Error. Can't connect to your MySQL server.</b> You need to have PHP 5.2+ and MySQL 5.5+ with mysqli extension activated. <a href='http://crybit.com/how-to-enable-mysqli-extension-on-web-server/'>Instruction &#187;</a>\n";
				echo "<br />Also <b>please check DB username/password in file cryptobox.config.php</b>\n";
				die("<br />Server has returned error - <b>".$err."</b>");
			}
			$mysqli->query("SET NAMES utf8");
		}

		$query = $mysqli->query($sql);

		if ($query === FALSE)
        {
            if (stripos(str_replace('"', '', str_replace("'", "", $mysqli->error)), "crypto_payments doesnt exist"))
            {
                // Try to create new table - https://github.com/cryptoapi/Payment-Gateway#mysql-table
                $mysqli->query("CREATE TABLE `crypto_payments` (
                              `paymentID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `boxID` int(11) unsigned NOT NULL DEFAULT '0',
                              `boxType` enum('paymentbox','captchabox') NOT NULL,
                              `orderID` varchar(50) NOT NULL DEFAULT '',
                              `userID` varchar(50) NOT NULL DEFAULT '',
                              `countryID` varchar(3) NOT NULL DEFAULT '',
                              `coinLabel` varchar(6) NOT NULL DEFAULT '',
                              `amount` double(20,8) NOT NULL DEFAULT '0.00000000',
                              `amountUSD` double(20,8) NOT NULL DEFAULT '0.00000000',
                              `unrecognised` tinyint(1) unsigned NOT NULL DEFAULT '0',
                              `addr` varchar(34) NOT NULL DEFAULT '',
                              `txID` char(64) NOT NULL DEFAULT '',
                              `txDate` datetime DEFAULT NULL,
                              `txConfirmed` tinyint(1) unsigned NOT NULL DEFAULT '0',
                              `txCheckDate` datetime DEFAULT NULL,
                              `processed` tinyint(1) unsigned NOT NULL DEFAULT '0',
                              `processedDate` datetime DEFAULT NULL,
                              `recordCreated` datetime DEFAULT NULL,
                              PRIMARY KEY (`paymentID`),
                              KEY `boxID` (`boxID`),
                              KEY `boxType` (`boxType`),
                              KEY `userID` (`userID`),
                              KEY `countryID` (`countryID`),
                              KEY `orderID` (`orderID`),
                              KEY `amount` (`amount`),
                              KEY `amountUSD` (`amountUSD`),
                              KEY `coinLabel` (`coinLabel`),
                              KEY `unrecognised` (`unrecognised`),
                              KEY `addr` (`addr`),
                              KEY `txID` (`txID`),
                              KEY `txDate` (`txDate`),
                              KEY `txConfirmed` (`txConfirmed`),
                              KEY `txCheckDate` (`txCheckDate`),
                              KEY `processed` (`processed`),
                              KEY `processedDate` (`processedDate`),
                              KEY `recordCreated` (`recordCreated`),
                              KEY `key1` (`boxID`,`orderID`),
                              KEY `key2` (`boxID`,`orderID`,`userID`),
                              UNIQUE KEY `key3` (`boxID`, `orderID`, `userID`, `txID`, `amount`, `addr`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;");

                $query = $mysqli->query($sql);  // re-run previous query
            }
            if ($query === FALSE) die("MySQL Error: ".$mysqli->error."; SQL: $sql");
        }

		if (is_object($query) && $query->num_rows)
		{
			while($row = $query->fetch_object())
			{
				if ($f)
				{
					if (property_exists($row, "idx")) $x = true;
					$c = count(get_object_vars($row));
					if ($c > 2 || ($c == 2 && !$x)) $g = true;
					elseif (!property_exists($row, "nme")) die("Error in run_sql() - 'nme' not exists! SQL: $sql");
					$f = false;
				}

				if (!$g && $query->num_rows == 1 && property_exists($row, "nme")) return $row->nme;
				elseif ($x) $res[$row->idx] = ($g) ? $row : $row->nme;
				else $res[] = ($g) ? $row : $row->nme;
			}
		}
		elseif (stripos($sql, "insert ") !== false) $res = $mysqli->insert_id;

		if (is_object($query)) $query->close();
		if (is_array($res) && count($res) == 1 && isset($res[0]) && is_object($res[0])) $res = $res[0];

        return response()->json($res, 201);
    }

    /**
     * get Latest order id
     *
     * @return status
     */
    public function getOrder(Request $request){
        return response()->json(Payorders::count(), 201);
    }

    /**
     * Create payment request
     *
     * @return status
     */
    public function setCrypto(Request $request){
        $curl = curl_init();

        $type = explode('_', $request->order)[0];

        if($type == 'activate'){
            $curl1 = curl_init();
            curl_setopt_array($curl1, array(
                // CURLOPT_URL => 'https://api.sandbox.nowpayments.io/v1/estimate?amount='.$request->amount.'&currency_from=usd&currency_to='.$request->token,
                CURLOPT_URL => 'https://api.nowpayments.io/v1/estimate?amount='.$request->amount.'&currency_from=usd&currency_to='.$request->token,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                //   'x-api-key: VK4BTV9-C6J4R5P-JPP2V43-FARF5ZG	',
                  'x-api-key: S7NVMMP-T7VMSG5-JKYV2WW-RP2V62N',
                  'Content-Type: application/json',
                  'Cookie: __cfduid=d3873d34fbd9d9a14f2bedfd5569172ff1616010577'
                ),
              ));

              $response = json_decode(curl_exec($curl1), true);

              curl_close($curl1);

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://api.sandbox.nowpayments.io/v1/payment',
                CURLOPT_URL => 'https://api.nowpayments.io/v1/payment',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "price_amount": '.$request->amount.',
                "price_currency": "usd",
                "pay_amount": "'.$response['estimated_amount'].'",
                "pay_currency": "'.$request->token.'",
                "ipn_callback_url": "https://calahex.io/api/wallet/getCrypto",
                "order_id": "'.$request->order.'",
                "order_description": ""
                }',
                CURLOPT_HTTPHEADER => array(
                // 'x-api-key: VK4BTV9-C6J4R5P-JPP2V43-FARF5ZG	',
                'x-api-key: S7NVMMP-T7VMSG5-JKYV2WW-RP2V62N',
                'Content-Type: application/json',
                'Cookie: __cfduid=d3873d34fbd9d9a14f2bedfd5569172ff1616010577'
                ),
            ));

        }
        if($type == 'deposit')
            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://api.sandbox.nowpayments.io/v1/payment',
                CURLOPT_URL => 'https://api.nowpayments.io/v1/payment',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "price_amount": 1,
                "price_currency": "usd",
                "pay_amount": '.$request->amount.',
                "pay_currency": "'.$request->token.'",
                "ipn_callback_url": "https://calahex.io/api/wallet/getCrypto",
                "order_id": "'.$request->order.'",
                "order_description": ""
                }',
                CURLOPT_HTTPHEADER => array(
                // 'x-api-key: VK4BTV9-C6J4R5P-JPP2V43-FARF5ZG	',
                'x-api-key: S7NVMMP-T7VMSG5-JKYV2WW-RP2V62N',
                'Content-Type: application/json',
                'Cookie: __cfduid=d3873d34fbd9d9a14f2bedfd5569172ff1616010577'
                ),
            ));

        $response = json_decode(curl_exec($curl), true);
        // $crypto_name = '';
        // if($response['pay_currency'] == 'BTC')  $crypto_name = "bitcoin";
        // if($response['pay_currency'] == 'ETH')  $crypto_name = "ethereum";
        // if($response['pay_currency'] == 'USDT')  $crypto_name = "usdt";
        // if($response['pay_currency'] == 'SXP')  $crypto_name = "swipe";
        // if($response['pay_currency'] == 'REP')  $crypto_name = "augur";
        // if($response['pay_currency'] == 'UNI')  $crypto_name = "uniswap";
        // if($response['pay_currency'] == 'LINK')  $crypto_name = "chainlink";

        curl_close($curl);

        $payorder = new Payorders();
        $payorder->user_id = $request->user_id;
        $payorder->payment_id = $response['payment_id'];
        $payorder->unit = $request->token;
        $payorder->address = $response['pay_address'];
        $payorder->reference = explode('_',$request->order)[1];
        $payorder->payment_type = explode('_',$request->order)[0];
        $payorder->amount = $request->amount;
        $payorder->status = 'requesting';
        $payorder->save();

        return response()->json($response, 201);
    }

    /**
     * get Crypto payment notification result
     *
     * @return status
     */
    public function getLimit(Request $request){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.nowpayments.io/v1/min-amount?currency_from='.$request->token.'&currency_to='.$request->token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'x-api-key: S7NVMMP-T7VMSG5-JKYV2WW-RP2V62N',
            'Cookie: __cfduid=d3873d34fbd9d9a14f2bedfd5569172ff1616010577'
        ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return response()->json($response['min_amount'], 201);
    }

    public function fiatdeposit(Request $request) {
        $user_id = $request->user_id;
        $currency = $request->currency;
        $amount = $request->amount;

        $order = new Payorders();
        $order->user_id = $user_id;
        $order->payment_id = 0;
        $order->reference = 'fiat';
        $order->unit = $currency;
        $order->amount = $amount;
        $order->payment_type = 'deposit';
        $order->status = 'requesting';
        $order->save();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.nowpayments.io/v1/invoice',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "price_amount": '.$amount.',
        "price_currency": "'.$currency.'",
        "order_id": "FiatDeposit-'.$user_id.'-'.Payorders::count().'",
        "order_description": "Apple Macbook Pro 2019 x 1",
        "ipn_callback_url": "https://nowpayments.io",
        "success_url": "https://calahex.com/wallet/exchange-account/wallet?key='.$order->id.'",
        "cancel_url": "https://calahex.com/buy-crypto"
        }',
        CURLOPT_HTTPHEADER => array(
            'x-api-key: S7NVMMP-T7VMSG5-JKYV2WW-RP2V62N',
            'Content-Type: application/json',
            'Cookie: __cfduid=d3873d34fbd9d9a14f2bedfd5569172ff1616010577'
        ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return response()->json($response['invoice_url'], 201);
    }

    public function fiatdepositcheck(Request $request) {
        $order_id = $request->key;
        $order = Payorders::find($order_id);
        if($order && $order->status == 'requesting'){
            $order->status = 'done';
            $order->save();

            $wallet_id = Wallets::where('user_id', $order->user_id)->first();
            $usdt_token_id = Tokens::where('token_symbol', 'USDT')->first()->id;
            $account = Accounts::where('wallet_id', $wallet_id)->where('account_type', 'exchange')->where('token_id', $usdt_token_id)->first();

            if($order->currency == 'USD')
                $amount = floatval($order->amount) * 0.93;
            if($order->currency == 'EUR')
                $amount = floatval($order->amount) * 1.14;
            if($order->currency == 'AWG')
                $amount = floatval($order->amount) * 0.51;

            if($account){
                $account->amount = floatval($account->amount) + floatval($amount);
                $account->save();
            }

            else {
                $new_account = new Accounts();
                $new_account->wallet_id = $wallet_id;
                $new_account->account_type = 'exchange';
                $new_account->token_id = $usdt_token_id;
                $new_account->amount = $amount;
                $new_account->save();
            }

            return response()->json('yes', 201);
        }
        return response()->json('no', 201);
    }

    public function depositfiatconfirm(Request $request) {

        $user = User::findOrFail($request->user_id);
        $profile = Profile::where('user_id', '=', $request->user_id)->first();
        $maildata=array(
        "email" => $user->email,
        "amount" => $request->amount,
        "currency" => $request->currency,
        "full_name" => $profile->firstname.' '.$profile->lastname,
        "country" => $profile->country,
        "street" => $profile->street,
        "city" => $profile->city,
        "postal_code" => $profile->postal_code,
        "birthday" => $profile->birthday,
        "phone_number" => $profile->phone_number,
        "corres_bank" => $request->corres_bank,
        "swift_code" => $request->swift_code,
        "swift_rbcroyal" => $request->swift_rbcroyal,
        "address_rbcroyal" => $request->address_rbcroyal,
        "benefit_accountname" => $request->benefit_accountname,
        "benefit_accountnumber" => $request->benefit_accountnumber,
        "benefi_address" => $request->benefi_address,
        "detail_payment" => $request->detail_payment);


        $details = [
            'to' => 'antonykamermans@calahex.com',
            'from' => 'customerservice@calahex.io',
            'subject' => 'Calahex.com Deposit Fiat Confirmation',
            'title' => 'Calahex.com Deposit Fiat Confirmation',
            "body" 	=> $maildata,
            'type' => 'depositfiatconfirm'
        ];
        \Mail::to('antonykamermans@calahex.com')->send(new \App\Mail\Mailer($details));
        return response()->json('yes', 201);
    }
    /**
     * get Crypto payment notification result
     *
     * @return status
     */
    public function getResult(Request $request){
        $payorder = Payorders::where('payment_id', strval($request->payment_id))->first();
        if($payorder && $payorder->status == 'cancelled')
            return response()->json('cancelled', 201);
        if($payorder && $payorder->amount_left){
            $user = User::find($payorder->user_id)->first();
            if($user){
                try {
                    $details = [
                        'to' => $user->email,
                        'from' => 'customerservice@calahex.io',
                        'subject' => 'Calahex.com Deposit Successful',
                        'title' => 'Calahex.com Deposit Successful',
                        "amount" => $payorder->amount,
                        "unit" => $payorder->unit,
                        'type' => 'depositsuccess'
                    ];

                    \Mail::to($user->email)->send(new \App\Mail\Mailer($details));
                    if (\Mail::failures()) {
                        return response()->json([
                            'status'  => false,
                            'data'    => $details,
                            'message' => 'Not sending mail.. retry again...'
                        ]);
                    }
                    return response()->json([
                        'message' => 'Successfully deposit! Message Sent!'
                    ], 201);
                } catch (Exception $e) {
                    return response()->json([
                        'message' => $e
                    ], 201);
                }
            }
            return response()->json('Wrong User', 201);
        }

        return response()->json('pending', 201);
    }
    public function getResultMail(Request $request){
        $payorder = Payorders::where('payment_id', strval($request->payment_id))->first();

        if($payorder){
            $user = User::findOrFail($payorder->user_id);
            if($user){
                try {
                    $profile = Profile::where('user_id', '=', $payorder->user_id)->first();
                    $maildata=array(
                        "email" => $user->email,
                        "full_name" => $profile->firstname.' '.$profile->lastname,
                        "country" => $profile->country,
                        "phone_number" => $profile->phone_number,
                        "amount" => $request->amount,
                        "currency" => $request->currency,
                        "date" =>  Carbon::now()->format('Y-m-d'),
                        );
                    $details = [
                        'to' => 'antonykamermans@calahex.com',
                        'from' => 'customerservice@calahex.io',
                        'subject' => 'Calahex.com Deposit Successful',
                        'title' => 'Calahex.com Deposit Successful',
                        "body" 	=> $maildata,
                        'type' => 'depositsuccesstoadmin'
                    ];
                    
                    \Mail::to('antonykamermans@calahex.com')->send(new \App\Mail\Mailer($details));

                    if (\Mail::failures()) {
                        return response()->json([
                            'status'  => false,
                            'data'    => $details,
                            'message' => 'Not sending mail.. retry again...'
                        ]);
                    }

                    return response()->json([
                        'message' => 'Successfully deposit! Message Sent!'
                    ], 201);
                } catch (Exception $e) {
                    return response()->json([
                        'message' => $e
                    ], 201);
                }
            }
            return response()->json('Wrong User', 201);
        }

    }

    /**
     * get Crypto payment notification
     *
     * @return status
     */
    public function getCrypto(Request $request){
        $query = new Query;
        $query->query_string = $request->payment_id." (".$request->pay_address.") -> ".$request->pay_amount.$request->pay_currency."/".$request->actually_paid.$request->pay_currency."->".$request->payment_status;
        $query->save();

        $payorder = Payorders::where('payment_id', strval($request->payment_id))->first();
        if($payorder){
            if($request->payment_status == 'waiting'){
                if($payorder->payment_type == 'deposit'){
                    $token_id = Tokens::where('token_symbol', strval($payorder->unit))->first()->id;
                    $wallet_id = Wallets::where('user_id', strval($payorder->user_id))->first()->id;
                    if(!$wallet_id || !$token_id)
                        return response()->json('error', 201);
                    $account = Accounts::where('token_id', strval($token_id))->where('wallet_id', strval($wallet_id))->where('account_type', 'exchange')->first();
                    if($account){
                        $account->amount = floatval($account->amount)+floatval($request->actually_paid);
                        $account->save();
                    }
                    else{
                        $account = new Accounts();
                        $account->wallet_id = $wallet_id;
                        $account->token_id = $token_id;
                        $account->account_type = 'exchange';
                        $account->amount = floatval($request->actually_paid);
                        $account->save();
                    }
                }
                if($payorder->payment_type == 'activate' && $request->pay_amount == $request->actually_paid){
                    $wallet = Wallets::where('user_id', strval($payorder->user_id))->first();
                    $user = User::where('id', intval($payorder->user_id))->first();
                    if(!$wallet || !$user)
                        return response()->json('error', 201);
                    if($payorder->reference == 'margin'){
                        $wallet->margin_paid_at = date('Y-m-d h:i:s');
                        // if($user->video_verified_at)
                            $wallet->margin_activated_at = date('Y-m-d h:i:s');
                        $wallet->save();
                    }
                    if($payorder->reference == 'pool'){
                        $wallet->pool_paid_at = date('Y-m-d h:i:s');
                        // if($user->auth_verified_at)
                            $wallet->pool_activated_at = date('Y-m-d h:i:s');
                        $wallet->save();
                    }
                    if($payorder->reference == 'saving'){
                        $wallet->saving_paid_at = date('Y-m-d h:i:s');
                        // if($user->phone_verified_at)
                            $wallet->savings_activated_at = date('Y-m-d h:i:s');
                        $wallet->save();
                    }
                }
            }
            $payorder->status = $request->payment_status;
            $payorder->amount = $request->pay_amount;
            $payorder->amount_left = $request->pay_amount - $request->actually_paid;
            $payorder->save();

        }

        return response()->json($payorder, 201);
    }

    public function withdraw(Request $request){
        $user_id = $request->user_id;
        $token = $request->coin;
        $amount = $request->amount;
        $type = $request->type;
        $address = $request->address;
        $token_id = Tokens::where('token_symbol', $token)->first()->id;
        $wallet_id = Wallets::where('user_id', $user_id)->first()->id;

        $payorder = new Payorders();
        $payorder->user_id = $user_id;
        $payorder->reference = 'crypto';
        $payorder->reference = $type;
        $payorder->unit = $token;
        $payorder->amount = $amount;
        $payorder->payment_type = 'withdraw';
        $payorder->status = 'done';
        $payorder->address = $address;
        $payorder->save();

        $user = User::find($user_id);
        if($user){
            try {
                $details = [
                    'to' => $user->email,
                    'from' => 'customerservice@calahex.io',
                    'subject' => 'Calahex.com Withdrawal Successful',
                    'title' => 'Calahex.com Withdrawal Successful',
                    "amount" => $amount,
                    "token" => $token,
                    'type' => 'withdrawsuccess'
                ];
                \Mail::to($user->email)->send(new \App\Mail\Mailer($details));
                if (\Mail::failures()) {
                    return response()->json([
                        'status'  => false,
                        'data'    => $details,
                        'message' => 'Not sending mail.. retry again...'
                    ]);
                }
                return response()->json([
                    'message' => 'Successfully withdraw! Message Sent!'
                ], 201);
            } catch (Exception $e) {
                return response()->json([
                    'message' => $e
                ], 201);
            }
        }

        $account = Accounts::where('wallet_id', $wallet_id)->where('token_id', $token_id)->where('account_type', 'exchange')->first();
        if($account){
            $account->amount = floatval($account->amount)-floatval($amount);
            $account->save();
            return $account->amount;
        }
        else{
            $account_new = new Accounts();
            $account_new->wallet_id = $wallet_id;
            $account_new->token_id = $token_id;
            $account->account_type = 'exchange';
            $account->amount = $amount;
            $account->save();
            return $account->amount;
        }
    }
}
