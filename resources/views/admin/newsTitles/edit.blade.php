@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Edit') }}: {{ $newsTitle->name }}</div>
                    <div class="card-body">
                        <form method="POST" action="/news_titles/{{ $newsTitle->id }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label>Name</label>
                                <input class="form-control" type="text" placeholder="{{ __('Name') }}" value="{{ $newsTitle->name }}" name="name" required autofocus>
                            </div>

                            <button class="btn btn-block btn-success" type="submit">{{ __('Save') }}</button>
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
