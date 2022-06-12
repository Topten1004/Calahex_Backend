<?php
namespace App\Http\Controllers;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallets;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use App\Mail\Mailer;
use Mail;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'referral' => 'string',
            'menurole' => 'string'
        ]);

        $user = new User([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'referral_from' => $request->referral,
            'menurole' => $request->role
        ]);
        $user->save();

        $wallet = new Wallets([
            'user_id' => $user->id
        ]);
        $wallet->save();

        $key = (ord($request->email[0]) * ord($request->email[1]) * ord($request->email[2])) % 1000000;

        try {
            $details = [
                'to' => $request->email,
                'from' => 'customerservice@calahex.io',
                'subject' => 'Calahex.com Email Confirmation',
                'title' => 'Calahex.com Email Confirmation',
                "body" 	=> $key,
                'type' => 'confirmemail'
            ];
            \Mail::to($request->email)->send(new \App\Mail\Mailer($details));
            if (Mail::failures()) {
                return response()->json([
                    'status'  => false,
                    'data'    => $details,
                    'message' => 'Not sending mail.. retry again...'
                ]);
            }
            return response()->json([
                'message' => 'Successfully created user! Message Sent!'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e
            ], 201);
        }
    }

    /**
     * Confirm user
     *
     * @param  [string] email
     * @param  [string] key
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        $key = (ord($user->email[0])*ord($user->email[1])*ord($user->email[2]))%1000000;

        if(User::where('email', $request->email)->count() == 0)
            return response()->json([
                'message' => 'email is not Valid'
            ], 500);
        elseif($request->password != $key)
            return response()->json([
                'message' => 'key is not Valid'
            ], 500);
        else{
            $user->verify_level = 1;
            $user->email_verified_at = date('Y-m-d h:i:s');
            $user->save();
            return response()->json([
                'message' => 'Successfully confirmed!',
            ], 201);
        }

    }

    public function profile(Request $request)
    {

//        $request->validate([
//            'first_name' => 'required|string',
//            'last_name'=>'required|string',
//            'country'=>'required|string',
//            'city' =>'required|string',
//            'street' =>'required|string',
//            'postal_code' =>'required|string',
//            'phone' =>'required|string',
//            'ip_address' =>'required|string',
//            'birthday' =>'required|string',
//            'language' =>'required|string',
//        ]);

        $user = User::where('email', $request->email)->first();
        $res = $user->id;
        $profile = Profile::where('user_id',$user->id)->first();
        if(isset($profile)){
            $profile->user_id = $user->id;
            $profile->country = $request->country;
            $profile->birthday = $request->birthday;
            $profile->city = $request->city;
            $profile->firstname = $request->first_name;
            $profile->lastname = $request->last_name;
//            $profile->ip_address = $request->ip_address;
            $profile->language = $request->language;
//            $profile->phone_number = $request->phone;
            $profile->postal_code = $request->postal_code;
            $profile->street = $request->street;

            $profile->save();
        }
        else {
            $profile = New Profile();
            $profile->user_id = $user->id;
            $profile->country = $request->country;
            $profile->birthday = $request->birthday;
            $profile->city = $request->city;
            $profile->firstname = $request->first_name;
            $profile->lastname = $request->last_name;
//            $profile->ip_address = $request->ip_address;
            $profile->language = $request->language;
//            $profile->phone_number = $request->phone;
            $profile->postal_code = $request->postal_code;
            $profile->street = $request->street;

            $profile->save();
        }

        return response()->json([
            'message' => 'Successfully verified user phone! Message Sent!',
            'id' => $res
        ], 201);

    }

    public function phone(Request $request)
    {

//        $request->validate([
//            'first_name' => 'required|string',
//            'last_name'=>'required|string',
//            'country'=>'required|string',
//            'city' =>'required|string',
//            'street' =>'required|string',
//            'postal_code' =>'required|string',
//            'phone' =>'required|string',
//            'ip_address' =>'required|string',
//            'birthday' =>'required|string',
//            'language' =>'required|string',
//        ]);

        $user = User::where('id', $request->id)->first();
        $user->phone_verified_at = Carbon::now();
        $user->save();

        return response()->json([
            'message' => 'Successfully verified user phone! Message Sent!'
        ], 201);

    }

    public function two(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        $profile = Profile::where('user_id',$user->id)->first();
        if(isset($profile)){
            $profile->mother_name = $request->mother_name;
            $profile->father_name = $request->father_name;
            $profile->nick_name = $request->nick_name;
            $profile->hobby = $request->hobby;
            $profile->best_friend = $request->best_friend;
            $profile->save();
            $user->auth_verified_at = Carbon::now();

        }
        else {
            $profile = New Profile();

            $profile->mother_name = $request->mother_name;
            $profile->father_name = $request->father_name;
            $profile->nick_name = $request->nick_name;
            $profile->hobby = $request->hobby;
            $profile->best_friend = $request->best_friend;
            $profile->save();
            $user->auth_verified_at = Carbon::now();
        }
        $user->save();

        return response()->json([
            'message' => 'Successfully verified user 2FA! Message Sent!'
        ], 201);

    }
    /**
     * Send change password link to user
     *
     * @param  [string] email
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);

        $key = (ord($request->email[3]) * ord($request->email[4]) * ord($request->email[5])) % 1000000;

        try {
            $details = [
                'to' => $request->email,
                'from' => 'customerservice@calahex.io',
                'subject' => 'Calahex.com Change Password',
                'title' => 'Calahex.com Change Password',
                "body" 	=> $key,
                'type' => 'changepassword'
            ];
            \Mail::to($request->email)->send(new \App\Mail\Mailer($details));
            if (Mail::failures()) {
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
    /**
     * Send change password link to user
     *
     * @param  [string] email
     */
    public function change(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user)
            return response()->json([
                'Wrong Email!'
            ], 201);
        $key = (ord($request->email[3]) * ord($request->email[4]) * ord($request->email[5])) % 1000000;
        if($request->key != $key)
            return response()->json([
                'Wrong Key!'
            ], 201);

        if($request->password != $request->password_confirmation)
            return response()->json([
                'Wrong Confirm!'
            ], 201);

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'Successfully changed!'
        ], 201);

    }
    /**
     * ReConfirm user
     *
     * @param  [string] email
     */
    public function reconfirm(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);

        $key = (ord($request->email[0]) * ord($request->email[1]) * ord($request->email[2])) % 1000000;

        try {
            $details = [
                'to' => $request->email,
                'from' => 'customerservice@calahex.io',
                'subject' => 'Calahex.com Email Confirmation',
                'title' => 'Calahex.com Email Confirmation',
                "body" 	=> $key,
                'type' => 'confirmemail'
            ];
            \Mail::to($request->email)->send(new \App\Mail\Mailer($details));
            if (Mail::failures()) {
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

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
//            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Please input correct email or password'
            ], 401);

        if(is_null(User::where('email', $request->email)->first()->verify_level))
            return response()->json([
                'message' => 'Please confirm your email'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        $profile = Profile::where('user_id', $request->user()->id)->first();
        if($profile){
            $name = $profile->firstname." ".$profile->lastname;
        }
        else
            $name = "";

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'user_id'=>$request->user()->id,
            'user_name'=>$name,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
