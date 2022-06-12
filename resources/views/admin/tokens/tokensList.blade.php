@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Token') }}</div>
                    <div class="card-body">
                        <div class="row">
                          <a href="{{ route('tokens.create') }}" class="btn btn-primary m-2">{{ __('Add Token') }}</a>
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Decimal</th>
{{--                            <th>Token ID</th>--}}
                            <th>Whitepaper</th>
                            <th>Pair Type</th>
                            <th>Logo</th>
                            <th>Status</th>
                            <th colspan="4">Process</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($tokens as $token)
                            <tr>
                              <td><strong>{{ $token->token_name }}</strong></td>
                              <td>{{ $token->token_symbol }}</td>
                              <td>{{ $token->token_decimal }}</td>
{{--                              <td>{{ $token->token_id }}</td>--}}
                              <td>
                                  <a href="{{ $token->token_whitepaper }}" target="_blank">
                                      {{ $token->token_whitepaper }}
                                  </a>
                              </td>
                              <td>{{ $token->token_pair_type }}</td>
                              <td><img src="{{ $token->token_logo }}" class="token-logo" /></td>
                              <td>
                                <span class="{{ $token->token_status->class }}">
                                    {{ $token->token_status->name }}
                                </span>
                              </td>
                              <td>
                                <a href="{{ url('/tokens/' . $token->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                              </td>
                              <td>
                                  @if($token->status == 1)
                                      <form action="{{ route('tokens.approve', $token->id ) }}" method="POST">
                                          @method('PUT')
                                          @csrf
                                          <button class="btn btn-block btn-success">Approve</button>
                                      </form>
                                  @else
                                      <form action="{{ route('tokens.approve', $token->id ) }}" method="POST">
                                          @method('PUT')
                                          @csrf
                                          <button class="btn btn-block btn-success" disabled>Approve</button>
                                      </form>
                                  @endif
                              </td>
                              <td>
                                  @if($token->status == 1)
                                      <form action="{{ route('tokens.block', $token->id ) }}" method="POST">
                                          @method('PUT')
                                          @csrf
                                          <button class="btn btn-block btn-danger" disabled>Block</button>
                                      </form>
                                  @elseif($token->status == 2)
                                      <form action="{{ route('tokens.block', $token->id ) }}" method="POST">
                                          @method('PUT')
                                          @csrf
                                          <button class="btn btn-block btn-danger">Block</button>
                                      </form>
                                  @elseif($token->status == 3)
                                      <form action="{{ route('tokens.block', $token->id ) }}" method="POST">
                                          @method('PUT')
                                          @csrf
                                          <button class="btn btn-block btn-danger">Unblock</button>
                                      </form>
                                  @endif
                              </td>
                              <td>
                                <form action="{{ route('tokens.destroy', $token->id ) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-block btn-danger">Delete</button>
                                </form>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
{{--                        <div class="d-flex justify-content-center pagination">--}}
{{--                            {!! $tokens->links() !!}--}}
{{--                        </div>--}}
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection

