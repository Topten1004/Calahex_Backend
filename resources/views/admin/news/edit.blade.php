@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Edit') }}: {{ $news->title }}</div>
                    <div class="card-body">
                        <form method="POST" action="/news/{{ $news->id }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label>Title</label>
                                <select class="form-control" name="title">
                                    @foreach($titles as $title)
                                        <option value="{{ $title->id }}">{{ $title->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Content</label>
                                    <textarea class="form-control" id="textarea-input" name="content" rows="9" placeholder="{{ __('Content..') }}" required>{{ $news->content }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Applies to date</label>
                                    <input type="date" class="form-control" name="applies_to_date" value="{{ $news->applies_to_date }}" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Status</label>
                                    <select class="form-control" name="status_id">
                                        @foreach($statuses as $status)
                                            @if( $status->id == $news->status_id )
                                                <option value="{{ $status->id }}" selected="true">{{ $status->name }}</option>
                                            @else
                                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-block btn-success" type="submit">{{ __('Save') }}</button>
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
