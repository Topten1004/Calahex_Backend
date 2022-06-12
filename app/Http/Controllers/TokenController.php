<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tokens;
use Illuminate\Support\Facades\Storage;

class TokenController extends Controller
{
    /**
     * Get the token info list in the platform
     *
     * @return [json] token info list json
     */
    public function list(Request $request)
    {
        $token_list = Tokens::select('token_pair_type', 'token_logo', 'token_symbol', 'token_decimal', 'token_whitepaper', 'token_name')->where('status', 2)->where('for_cefi',1)->get();

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

        $response_ticker = json_decode(curl_exec($curl));

        curl_close($curl);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://poloniex.com/public?command=return24hVolume',
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

        $response_24hVolume = json_decode(curl_exec($curl));

        curl_close($curl);

        $result = [];

        foreach($token_list as $token){
            $data = $token;
//            foreach($response_ticker as $key => $value){
//                $pair_left = explode('/', $token->token_pair_type)[0];
//                $pair_right = explode('/', $token->token_pair_type)[1];
//                $key_left = explode('_', $key)[0];
//                $key_right = explode('_', $key)[1];
//                if(($key_left == $pair_left && $key_right == $pair_right)||($key_right == $pair_left && $key_left == $pair_right)){
//                    $data["token_last_price"] = $value->last;
//                    $data["token_24h_change"] = $value->percentChange*100;
//                    $data["token_24h_high"] = $value->high24hr;
//                    $data["token_24h_low"] = $value->low24hr;
//                }
//            }
//            foreach($response_24hVolume as $key => $value){
//                $key_pair = explode('_', $key);
//                $key_left = $key_pair[0];
//                if(count($key_pair)>1){
//                    $key_right = $key_pair[1];
//                    if(($key_left == $pair_left && $key_right == $pair_right)||($key_right == $pair_left && $key_left == $pair_right)){
//                        foreach($value as $key_volume => $val_volume)
//                            if($key_volume == $pair_right)
//                                $data["token_24h_volume"] = $val_volume;
//                    }
//                }
//            }
            $result[] = $data;
        }
        return response()->json($result, 201);
    }

    /**
     * Create new token
     *
     * @return [json] success or failure
     */
    public function create(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string',
//            'token_id' => 'required|string|unique:tokens',
            'token_symbol' => 'required|string',
            'token_decimal' => 'required|string',
            'token_pair_type' => 'required|string',
            'token_logo' => 'required',
            'token_whitepaper' => 'required',
        ]);

        $token = new Tokens([
            'token_name' => $request->token_name,
//            'token_id' => $request->token_id,
            'token_symbol' => $request->token_symbol,
            'token_decimal' => $request->token_decimal,
            'token_pair_type' => $request->token_pair_type,
            'token_whitepaper' => 'whitepaper.pdf',
            'token_logo' => 'logo.png',
            'status' => 1,
        ]);
        $token->save();

        $whitepaper = $request->file('token_whitepaper');
        $destinationPath = 'uploads/tokens/whitepapers';
        $whitepaper->move($destinationPath, $token->id . '.pdf');
        $token->token_whitepaper = url('uploads/tokens/whitepapers/' . $token->id . '.pdf');

        $logo = $request->file('token_logo');
        $destinationPath = 'uploads/tokens/logos';
        $logo->move($destinationPath, $token->id . '.' . $logo->getClientOriginalExtension());
        $token->token_logo = url('uploads/tokens/logos/' . $token->id . '.' . $logo->getClientOriginalExtension());

        $token->save();

        return response()->json([
            'message' => 'Successfully created token!'
        ], 201);
    }
}
