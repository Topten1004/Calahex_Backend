<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tokens;
use App\Models\TokenStatus;
use App\Http\Controllers\Controller;
use Lcobucci\JWT\Token;

class TokensController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $tokens = Tokens::where('for_cefi', 0)->where('status', 2)->get();
        $tokens = Tokens::where('for_cefi', 1)->get();
        return view('admin.tokens.tokensList', ['tokens' => $tokens]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $statuses = TokenStatus::all();
        return view('admin.tokens.create', [ 'statuses' => $statuses ]);
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
            'token_name'           => 'required',
            'token_symbol'           => 'required',
            'token_decimal'           => 'required',
//            'token_id'           => 'required',
            'token_pair_type'           => 'required',
            'token_whitepaper'           => 'required',
            'token_logo'           => 'required',
            'status'         => 'required',
        ]);
        $token = new Tokens();
        $token->token_name   = $request->input('token_name');
        $token->token_symbol   = $request->input('token_symbol');
        $token->token_decimal   = $request->input('token_decimal');
//        $token->token_id   = $request->input('token_id');
        $token->token_whitepaper = 'token_whitepaper';
        $token->token_pair_type   = $request->input('token_pair_type');
        $token->token_logo   = 'token_logo';
        $token->status = $request->input('status');
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

        $request->session()->flash('message', 'Successfully created token');
        return redirect()->route('tokens.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $token = Tokens::with('token_status')->find($id);
        return view('admin.tokens.tokenShow', [ 'token' => $token ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function whitepaper($id)
    {
        $token = Tokens::with('token_status')->find($id);
        return response()->file($token->token_whitepaper);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $token = Tokens::find($id);
        $statuses = TokenStatus::all();
        return view('admin.tokens.edit', [ 'statuses' => $statuses, 'token' => $token ]);
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
        //var_dump('bazinga');
        //die();
        $validatedData = $request->validate([
            'token_name'           => 'required',
            'token_symbol'           => 'required',
            'token_decimal'           => 'required',
//            'token_id'           => 'required',
            'token_pair_type'           => 'required',
//            'token_whitepaper'           => 'required',
//            'token_logo'           => 'required',
            'status'         => 'required',
        ]);
        $token = Tokens::find($id);
        $token->token_name   = $request->input('token_name');
        $token->token_symbol   = $request->input('token_symbol');
        $token->token_decimal   = $request->input('token_decimal');
//        $token->token_id   = $request->input('token_id');
        $token->token_pair_type   = $request->input('token_pair_type');

        if($request->file('token_whitepaper')){
            $whitepaper = $request->file('token_whitepaper');
            $destinationPath = 'uploads/tokens/whitepapers';
            $whitepaper->move($destinationPath, $token->id . '.pdf');
            $token->token_whitepaper = url('uploads/tokens/whitepapers/' . $token->id . '.pdf');
        }

        if($request->file('token_logo')){
            $logo = $request->file('token_logo');
            $destinationPath = 'uploads/tokens/logos';
            $logo->move($destinationPath, $token->id . '.' . $logo->getClientOriginalExtension());
            $token->token_logo = url('uploads/tokens/logos/' . $token->id . '.' . $logo->getClientOriginalExtension());
        }

        $token->status = $request->input('status');
        $token->save();
        $request->session()->flash('message', 'Successfully edited token');
        return redirect()->route('tokens.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $token = Tokens::find($id);
        if($token){
            $token->delete();
        }
        return redirect()->route('tokens.index');
    }

    /**
     * Update the status of the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $token = Tokens::find($id);
        $token->status = 2;
        $token->save();
        return redirect()->route('tokens.index');
    }

    public function block($id)
    {
        $token = Tokens::find($id);
        $token->status == 2 ? $token->status = 3 : $token->status = 2;
        $token->save();
        return redirect()->route('tokens.index');
    }
}
