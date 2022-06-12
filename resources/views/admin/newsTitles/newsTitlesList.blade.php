@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('News Title') }}</div>
                    <div class="card-body">
                        <div class="row">
                          <a href="{{ route('news_titles.create') }}" class="btn btn-primary m-2">{{ __('Add News Title') }}</a>
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>Title</th>
                            <th colspan="3">Process</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($newsTitles as $news)
                            <tr>
                              <td><strong>{{ $news->name }}</strong></td>
                              <td>
                                <a href="{{ url('/news_titles/' . $news->id) }}" class="btn btn-block btn-primary">View</a>
                              </td>
                              <td>
                                <a href="{{ url('/news_titles/' . $news->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                              </td>
                              <td>
                                <form action="{{ route('news_titles.destroy', $news->id ) }}" method="POST">
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

