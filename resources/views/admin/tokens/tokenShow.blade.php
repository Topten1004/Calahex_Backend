@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Token: {{ $token->token_name }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('logout') }}"> @csrf<button class="btn btn-primary">{{ __('Logout') }}</button></form>
                        <br>
                        <h4>Name:</h4>
                        <p> {{ $token->token_name }}</p>
                        <h4>Symbol:</h4>
                        <p> {{ $token->token_symbol }}</p>
                        <h4>Decimal:</h4>
                        <p> {{ $token->token_decimal }}</p>
{{--                        <h4>Token ID:</h4>--}}
{{--                        <p> {{ $token->token_id }}</p>--}}
                        <h4>Whitepaper:</h4>
                        <p> {{ $token->token_whitepaper }}</p>
                        <h4>Pair Type:</h4>
                        <p> {{ $token->token_pair_type }}</p>
                        <h4>Logo:</h4>
                        <p> {{ $token->token_logo }}</p>
                        <h4> Status: </h4>
                        <p>
                            <span class="{{ $token->token_status->class }}">
                              {{ $token->token_status->name }}
                            </span>
                        </p>
                        <a href="{{ route('tokens.index') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection
