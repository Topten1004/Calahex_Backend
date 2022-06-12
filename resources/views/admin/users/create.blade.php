@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Create User') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label>E-mail</label>
                                <input class="form-control" type="text" placeholder="{{ __('E-mail') }}" name="email" required autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Password</label>
                                <input class="form-control" type="password" placeholder="{{ __('Password') }}" name="password" required>
                            </div>

                            <input class="form-control" type="hidden" name="menuroles" value="user">

                            <input class="form-control" type="hidden" name="status" value="0">

                            <button class="btn btn-block btn-success" type="submit">{{ __('Add') }}</button>
                            <a href="{{ route('news.index') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a>
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
