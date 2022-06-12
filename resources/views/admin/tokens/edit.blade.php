@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Edit') }}: {{ $token->name }}</div>
                    <div class="card-body">
                        <form method="POST" action="/tokens/{{ $token->id }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label>Name</label>
                                <input class="form-control" type="text" placeholder="{{ __('Name') }}" value="{{ $token->token_name }}" name="token_name" required autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Symbol</label>
                                <input class="form-control" type="text" placeholder="{{ __('Symbol') }}" value="{{ $token->token_symbol }}" name="token_symbol" required>
                            </div>

                            <div class="form-group row">
                                <label>Decimal</label>
                                <input class="form-control" type="number" placeholder="{{ __('Decimal') }}" value="{{ $token->token_decimal }}" name="token_decimal" required>
                            </div>

{{--                            <div class="form-group row">--}}
{{--                                <label>Token ID</label>--}}
{{--                                <input class="form-control" type="text" placeholder="{{ __('Token ID') }}" value="{{ $token->token_id }}" name="token_id" required>--}}
{{--                            </div>--}}

                            <div class="form-group row">
                                <label>Whitepaper</label>
                                <input class="form-control" type="file" placeholder="{{ __('Whitepaper') }}" name="token_whitepaper">
                            </div>

                            <div class="form-group row">
                                <label>Pair Type</label>
                                <input class="form-control" type="text" placeholder="{{ __('Pair Type') }}" value="{{ $token->token_pair_type }}" name="token_pair_type" required>
                            </div>

                            <div class="form-group row">
                                <label>Logo</label>
                                <input class="form-control" type="file" placeholder="{{ __('Logo') }}" name="token_logo">
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        @foreach($statuses as $status)
                                            @if( $status->id == $token->status_id )
                                                <option value="{{ $status->id }}" selected="true">{{ $status->name }}</option>
                                            @else
                                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-block btn-success" type="submit">{{ __('Save') }}</button>
                            <a href="{{ route('tokens.index') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a>
                        </form>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection

@section('javascript')

@endsection
