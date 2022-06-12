@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Create News') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('news_titles.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label>Name</label>
                                <input class="form-control" type="text" placeholder="{{ __('Name') }}" name="name" required autofocus>
                            </div>

                            <button class="btn btn-block btn-success" type="submit">{{ __('Add') }}</button>
                            <a href="{{ route('news_titles.index') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a>
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
