@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('News') }}</div>
                    <div class="card-body">
                        <div class="row">
                          <a href="{{ route('news.create') }}" class="btn btn-primary m-2">{{ __('Add News') }}</a>
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>Author</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Applies to date</th>
                            <th>Status</th>
                            <th colspan="3">Process</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($newses as $news)
                            <tr>
                              <td><strong>{{ $news->user->email }}</strong></td>
                              <td><strong>{{ $news->news_title->name }}</strong></td>
                              <td>{{ $news->content }}</td>
                              <td>{{ $news->applies_to_date }}</td>
                              <td>
                                  <span class="{{ $news->status->class }}">
                                      {{ $news->status->name }}
                                  </span>
                              </td>
                              <td>
                                <a href="{{ url('/news/' . $news->id) }}" class="btn btn-block btn-primary">View</a>
                              </td>
                              <td>
                                <a href="{{ url('/news/' . $news->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                              </td>
                              <td>
                                <form action="{{ route('news.destroy', $news->id ) }}" method="POST">
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
{{--                            {!! $newses->links() !!}--}}
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

