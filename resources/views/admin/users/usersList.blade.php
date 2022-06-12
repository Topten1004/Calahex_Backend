@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Users') }}</div>
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <a href="{{ route('users.create') }}" class=" btn btn-primary m-2">{{ __('Add User') }}</a>
                            <form method = "POST"  action ="{{route('users.search')}}" class="form-inline mr-4">
                                @csrf
                                <div class="form-group">
                                    <input value = "@php if(isset($searchText)) echo $searchText; @endphp" type="text" class="form-control" name="searchText" placeholder="Search">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="cil-search"></i></button>
                            </form>
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>E-mail</th>
                            <th>Roles</th>
                            <th>Email verified at</th>
                            <th>Phone verified at</th>
                            <th>2FA verified at</th>
                            <th>Video verified at</th>
                            <th>Status</th>
                            <th></th>
                            <th></th>
{{--                            <th></th>--}}
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($users as $user)
                            <tr>
                              <td>{{ $user->email }}</td>
                              <td>{{ $user->menuroles }}</td>
                              <td>{{ $user->email_verified_at }}</td>
                              <td>{{ $user->phone_verified_at }}</td>
                              <td>{{ $user->auth_verified_at }}</td>
                              <td>{{ $user->video_verified_at }}</td>
                              <td>{{ $user->status==0 ? 'Normal' : 'Blocked' }}</td>
                              <td>
                                <a href="{{ url('/users/' . $user->id) }}" class="btn btn-block btn-primary">Manage</a>
                              </td>
                              <td>
                                @if( $you->id !== $user->id )
                                  <a href="{{ url('/users/' . $user->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                                @endif
                              </td>
{{--                              <td>--}}
{{--                                @if( $you->id !== $user->id  && $user->status == 1)--}}
{{--                                  <form action="{{ route('users.unblock', $user->id ) }}" method="POST">--}}
{{--                                      @method('PUT')--}}
{{--                                      @csrf--}}
{{--                                      <button class="btn btn-block btn-success">Unblock</button>--}}
{{--                                  </form>--}}
{{--                                @elseif( $you->id !== $user->id  && $user->status == 0)--}}
{{--                                  <form action="{{ route('users.block', $user->id ) }}" method="POST">--}}
{{--                                      @method('PUT')--}}
{{--                                      @csrf--}}
{{--                                      <button class="btn btn-block btn-danger">Block</button>--}}
{{--                                  </form>--}}
{{--                                @endif--}}
{{--                              </td>--}}
                              <td>
                                @if( $you->id !== $user->id )
                                <form action="{{ route('users.destroy', $user->id ) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-block btn-danger">Delete User</button>
                                </form>
                                @endif
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection

