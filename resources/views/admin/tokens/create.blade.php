@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Create Token') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tokens.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label>Name</label>
                                <input class="form-control" type="text" placeholder="{{ __('Name') }}" name="token_name" required autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Symbol</label>
                                <input class="form-control" type="text" placeholder="{{ __('Symbol') }}" name="token_symbol" required>
                            </div>

                            <div class="form-group row">
                                <label>Decimal</label>
                                <input class="form-control" type="number" placeholder="{{ __('Decimal') }}" name="token_decimal" required>
                            </div>

{{--                            <div class="form-group row">--}}
{{--                                <label>Token ID</label>--}}
{{--                                <input class="form-control" type="text" placeholder="{{ __('Token ID') }}" name="token_id" required>--}}
{{--                            </div>--}}

                            <div class="form-group row">
                                <label>Whitepaper</label>
                                <input class="form-control" type="file" placeholder="{{ __('Whitepaper') }}" name="token_whitepaper" required>
                            </div>

                            <div class="form-group row">
                                <label>Pair Type</label>
                                <input class="form-control" type="text" placeholder="{{ __('Pair Type') }}" name="token_pair_type" required>
                            </div>

                            <div class="form-group row">
                                <label>Logo</label>
                                <input class="form-control" type="file" placeholder="{{ __('Logo') }}" name="token_logo" required>
                            </div>

                            <div class="form-group row">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-block btn-success" type="submit">{{ __('Add') }}</button>
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
