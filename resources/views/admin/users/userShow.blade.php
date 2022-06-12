@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> User {{ $user->name }}</div>
                    <div class="card-body">
                        <form action ="{{route('users.manage',$user->id)}}" method = "POST">
                            @csrf
                            <h5>E-mail: {{ $user->email }}<br/><br/></h5>
                            <h5>Estimated Total Balance: <strong>{{ $total_amount }}</strong> USDT<br/></h5>
                            
                            <div class = "row">
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6" style="padding-left:50px;">                                    
                                    <h3 style="text-align:center;margin-top:20px;margin-bottom:30px;margin-left:-50px;">Balances</h3>
                                    <table class="table table-responsive-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Token</th>
                                                <th>Exchange</th>
                                                <th>Margin</th>
                                                <th>Futures</th>
                                                <th>Savings</th>
                                                <th>Pool</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(json_decode($balances) as $balance)
                                                <tr>
                                                    <td>{{$balance->token_symbol}}</td>
                                                    <td>{{$balance->exchange_balance}}</td>
                                                    <td>{{$balance->margin_balance}}</td>
                                                    <td>{{$balance->futures_balance}}</td>
                                                    <td>{{$balance->savings_balance}}</td>
                                                    <td>{{$balance->pool_balance}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <h3 style="text-align:center;margin-top:20px;">Manage Balances</h3>
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                            <h5><br/><br/>Exchange:</h5>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Token</label>
                                            <select class="form-control" name="title" id="exchange_token" onchange="tokenSelect('exchange')">
                                                <option value="0"></option>
                                                @foreach($tokens as $token)
                                                    <option value="{{ $token->id }}">{{ $token->token_symbol }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Amount</label>
                                            <input type="number" step="0.0000001" class="form-control" id="exchange_amount" value="">
                                        </div>
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2" style="padding:10px 20px 10px 30px;">
                                            <br/>
                                            <a class="btn btn-success float-right" onclick="tokenSave('exchange')">Save</a>
                                        </div>
                                    </div>   
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                            <h5><br/><br/>Margin:</h5>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Token</label>
                                            <select class="form-control" name="title" id="margin_token" onchange="tokenSelect('margin')">
                                                <option value="0"></option>
                                                @foreach($tokens as $token)
                                                    <option value="{{ $token->id }}">{{ $token->token_symbol }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Amount</label>
                                            <input type="number" step="0.0000001" class="form-control" id="margin_amount" value="">
                                        </div>
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2" style="padding:10px 20px 10px 30px;">
                                            <br/>
                                            <a class="btn btn-success float-right" onclick="tokenSave('margin')">Save</a>
                                        </div>
                                    </div>          
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                            <h5><br/><br/>Futures:</h5>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Token</label>
                                            <select class="form-control" name="title" id="futures_token" onchange="tokenSelect('futures')">
                                                <option value="0"></option>
                                                @foreach($tokens as $token)
                                                    <option value="{{ $token->id }}">{{ $token->token_symbol }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Amount</label>
                                            <input type="number" step="0.0000001" class="form-control" id="futures_amount" value="">
                                        </div>
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2" style="padding:10px 20px 10px 30px;">
                                            <br/>
                                            <a class="btn btn-success float-right" onclick="tokenSave('futures')">Save</a>
                                        </div>
                                    </div>   
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                            <h5><br/><br/>Savings:</h5>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Token</label>
                                            <select class="form-control" name="title" id="savings_token" onchange="tokenSelect('savings')">
                                                <option value="0"></option>
                                                @foreach($tokens as $token)
                                                    <option value="{{ $token->id }}">{{ $token->token_symbol }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Amount</label>
                                            <input type="number" step="0.0000001" class="form-control" id="savings_amount" value="">
                                        </div>
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2" style="padding:10px 20px 10px 30px;">
                                            <br/>
                                            <a class="btn btn-success float-right" onclick="tokenSave('savings')">Save</a>
                                        </div>
                                    </div>           
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                            <h5><br/><br/>Pool:</h5>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Token</label>
                                            <select class="form-control" name="title" id="pool_token" onchange="tokenSelect('pool')">
                                                <option value="0"></option>
                                                @foreach($tokens as $token)
                                                    <option value="{{ $token->id }}">{{ $token->token_symbol }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4" style="padding:10px 20px 10px 30px;">
                                            <label>Amount</label>
                                            <input type="number" step="0.0000001" class="form-control" id="pool_amount" value="">
                                        </div>
                                        <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2" style="padding:10px 20px 10px 30px;">
                                            <br/>
                                            <a class="btn btn-success float-right" onclick="tokenSave('pool')">Save</a>
                                        </div>
                                    </div>   

                                    <h3 style="text-align:center;margin-top:30px;margin-bottom:30px;margin-left:-30px;">Deposit / Withdraw</h3>
                                    <div class = "row">
                                        <div class = "col-sm-6">
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:20px;">BTC</label>
                                                <input type="number" step="0.000001" class="form-control mr-2 @error('btc') is-invalid @enderror" name = "btc" value = "0" required>
                                            </div>
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:18px;">ETH</label>
                                                <input type="number" step="0.0001" class="form-control mr-2 @error('eth') is-invalid @enderror" name = "eth" value = "0" required>
                                            </div>
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:10px;">USDT</label>
                                                <input type="number" step="0.01" class="form-control mr-2 @error('usdt') is-invalid @enderror" name = "usdt" value = "0" required>
                                            </div>
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:10px;">SXP</label>
                                                <input type="number" step="0.01" class="form-control mr-2 @error('sxp') is-invalid @enderror" name = "sxp" value = "0" required>
                                            </div>
                                        </div>
                                        <div class = "col-sm-6">
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:20px;">REPV2</label>
                                                <input type="number" step="0.000001" class="form-control mr-2 @error('rep') is-invalid @enderror" name = "rep" value = "0" required>
                                            </div>
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:18px;">YFI</label>
                                                <input type="number" step="0.0001" class="form-control mr-2 @error('yfi') is-invalid @enderror" name = "yfi" value = "0" required>
                                            </div>
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:10px;">UNI</label>
                                                <input type="number" step="0.01" class="form-control mr-2 @error('uni') is-invalid @enderror" name = "uni" value = "0" required>
                                            </div>
                                            <div class="form-group d-flex">
                                                <label for="inputZip" style="margin-right:10px;">LINK</label>
                                                <input type="number" step="0.01" class="form-control mr-2 @error('link') is-invalid @enderror" name = "link" value = "0" required>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 style="text-align:center;margin-top:20px;margin-bottom:30px;margin-left:-50px;">Accounts</h3>
                                    <div class = "row">
                                        <h5>Futures: </h5>
                                        <div style="margin-left:12px;">
                                            <div class="custom-control d-inline custom-checkbox mb-3 mr-3" style="margin-left:70px">
                                                <input onclick = "futuresClick(this)" class="custom-control-input" type="checkbox" name = "future_verified" id="inlineCheckbox7" @php if(isset($wallet->futures_activated_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox7">verified</label>
                                            </div>
                                            <div class="custom-control d-inline custom-checkbox mb-3">
                                                <input class="custom-control-input" type="hidden" name = "future_activated" id="inlineCheckbox15" value = "@php if(isset($wallet->futures_activated_at)) echo "1"; else echo "0" @endphp">
                                                <input disabled class="custom-control-input" type="checkbox" name = "future_activated" id="inlineCheckbox11" @php if(isset($wallet->futures_activated_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox11">activated</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "row">
                                        <h5>Saving: </h5>
                                        <div style = "margin-left:30px;">
                                            <div class="custom-control d-inline custom-checkbox mb-3 mr-3">
                                                <input onclick = "savingPaidClick()" class="custom-control-input" type="checkbox" id="inlineCheckbox1" name = "saving_paid" @php if(isset($wallet->saving_paid_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox1">paid</label>
                                            </div>
                                            <div class="custom-control d-inline custom-checkbox mb-3 mr-3">
                                                <input onclick = "savingVerifiedClick()" class="custom-control-input" type="checkbox" name ="saving_verified" id="inlineCheckbox2" @php if(isset($user->phone_verified_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox2">verified</label>
                                            </div>
                                            <div  class="custom-control d-inline custom-checkbox mb-3">
                                                <input class="custom-control-input" type="hidden" name = "saving_activated" id="inlineCheckbox14" value = "@php if(isset($wallet->savings_activated_at)) echo "1"; else echo "0" @endphp">
                                                <input disabled class="custom-control-input" type="checkbox" name ="saving_activated" id="inlineCheckbox8" @php if(isset($wallet->savings_activated_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox8">activated</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "row">
                                        <h5>Pool: </h5>
                                        <div style = "margin-left:45px;">
                                            <div class="custom-control d-inline custom-checkbox mb-3 mr-3">
                                                <input onclick="poolPaidClick()" class="custom-control-input" type="checkbox" name = "pool_paid" id="inlineCheckbox3" @php if(isset($wallet->pool_paid_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox3">paid</label>
                                            </div>
                                            <div class="custom-control d-inline custom-checkbox mb-3 mr-3">
                                                <input onclick="poolVerifiedClick()" class="custom-control-input" type="checkbox" name ="pool_verified" id="inlineCheckbox4" @php if(isset($user->auth_verified_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox4">verified</label>
                                            </div>
                                            <div class="custom-control d-inline custom-checkbox mb-3">
                                                <input class="custom-control-input" type="hidden" name = "pool_activated" id="inlineCheckbox13" value = "@php if(isset($wallet->pool_activated_at)) echo "1"; else echo "0" @endphp">
                                                <input disabled class="custom-control-input" type="checkbox" name ="pool_activated" id="inlineCheckbox9" @php if(isset($wallet->pool_activated_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox9">activated</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "row">
                                        <h5>Margin: </h5>
                                        <div style = "margin-left:31px;">
                                            <div class="custom-control d-inline custom-checkbox mb-3 mr-3">
                                                <input onclick="marginPaidClick()" class="custom-control-input" name = "margin_paid" type="checkbox" id="inlineCheckbox5" @php if(isset($wallet->margin_paid_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox5">paid</label>
                                            </div>
                                            <div class="custom-control d-inline custom-checkbox mb-3 mr-3">
                                                <input onclick="marginVerifiedClick()" class="custom-control-input" type="checkbox" name = "margin_verified" id="inlineCheckbox6" @php if(isset($user->video_verified_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox6">verified</label>
                                            </div>
                                            <div class="custom-control d-inline custom-checkbox mb-3">
                                                <input class="custom-control-input" type="hidden" name = "margin_activated" id="inlineCheckbox12" value = "@php if(isset($wallet->margin_activated_at)) echo "1"; else echo "0"; @endphp">
                                                <input disabled class="custom-control-input" type="checkbox" name = "margin_activated" id="inlineCheckbox10" @php if(isset($wallet->margin_activated_at)) echo "checked"; @endphp>
                                                <label class="custom-control-label" for="inlineCheckbox10">activated</label>
                                            </div>
                                        </div>
                                    </div>  
                                    
                                                               
                                    <input type = "hidden" name = "id" value = "{{$user->id}}">
                                    <a href="{{ route('users.index') }}" class="float-right btn btn-primary" style="margin-top:10px;">{{ __('Back') }}</a>
                                    <button type="submit" class="float-right btn btn-success mr-2" style="margin-top:10px;">Save</button>                                        
                                </div>
                            </div>              
                        </form>
                        <br/><br/>
                        <table class="table table-responsive-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Token</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Datetime</th>
                                    <th>Price</th>
                                    <th>Amount(left)</th>
                                    <th>Amount(total)</th>
                                    <th>Detail</th>
                                    <th>Address</th>
                                    <th>PaymentID</th>
                                    <th>Account</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(json_decode($logData) as $log)
                                    <tr>
                                        <td>{{$log->type}}</td>
                                        <td>{{$log->token}}</td>
                                        <td>{{$log->amount}}</td>
                                        <td>{{$log->status}}</td>
                                        <td>{{$log->datetime}}</td>
                                        <td>{{$log->price}}</td>
                                        <td>{{$log->amount_left}}</td>
                                        <td>{{$log->amount_total}}</td>
                                        <td>{{$log->detail}}</td>
                                        <td>{{$log->address}}</td>
                                        <td>{{$log->payment_id}}</td>
                                        <td>{{$log->account}}</td>
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script>
        function logDetail(index){
            alert(index)
        }
        function tokenSelect(type){   
            var accounts = {!! $accounts !!}
            var selected = false;
            var token = document.getElementById(type+"_token").value;
            accounts.forEach(item=>{
                if(item['token_id'] == token && item['account_type'] == type){
                    document.getElementById(type+"_amount").value = Number(Math.round(item['amount']+'e7')+'e-7') ;
                    selected = true;
                }
            })
            if(selected == false) document.getElementById(type+"_amount").value = 0;
        }
        function tokenSave(type){
            var token = document.getElementById(type+"_token").value;
            var amount = document.getElementById(type+"_amount").value;
            $.ajax({
                url: "{{route('users.setBalance')}}",
                method: "POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    type: type,
                    token: token,
                    amount: amount,
                    user_id: {{$user->id}}
                },
                success: function(result){
                    if(result == 'No Change')
                        alert("No balance changed.")
                    else{
                        alert(type+" account's "+token+" balance is changed into "+amount);
                        location.reload();
                    }
                        
                }
            });
        }
        function futuresClick(e){
            document.getElementById('inlineCheckbox11').checked = e.checked;
            if(e.checked == true) document.getElementById('inlineCheckbox15').value = 1;
            else document.getElementById('inlineCheckbox15').value = 0;
            document.getElementById('inlineCheckbox2').checked = e.checked;
            if(document.getElementById('inlineCheckbox1').checked ==true && document.getElementById('inlineCheckbox2').checked ==true){
                document.getElementById('inlineCheckbox8').checked = true;
                document.getElementById('inlineCheckbox14').value = 1;
            }
            else {
                document.getElementById('inlineCheckbox8').checked = false;
                document.getElementById('inlineCheckbox14').value = 0;
            }
        }
        function savingPaidClick() {
            if(document.getElementById('inlineCheckbox1').checked ==true && document.getElementById('inlineCheckbox2').checked ==true){
                document.getElementById('inlineCheckbox8').checked = true;
                document.getElementById('inlineCheckbox14').value = 1;
            }
            else {
                document.getElementById('inlineCheckbox8').checked = false;
                document.getElementById('inlineCheckbox14').value = 0;
            }

        }
        function savingVerifiedClick() {

            if(document.getElementById('inlineCheckbox1').checked ==true && document.getElementById('inlineCheckbox2').checked ==true){
                document.getElementById('inlineCheckbox8').checked = true;
                document.getElementById('inlineCheckbox14').value = 1;
            }
            else {
                document.getElementById('inlineCheckbox8').checked = false;
                document.getElementById('inlineCheckbox14').value = 0;
            }
            document.getElementById('inlineCheckbox7').checked = document.getElementById('inlineCheckbox2').checked;
            document.getElementById('inlineCheckbox11').checked = document.getElementById('inlineCheckbox7').checked;
        }
        function poolPaidClick() {
            if(document.getElementById('inlineCheckbox3').checked ==true && document.getElementById('inlineCheckbox4').checked ==true){
                document.getElementById('inlineCheckbox9').checked = true;
                document.getElementById('inlineCheckbox13').value = 1;
            }
            else{
                document.getElementById('inlineCheckbox9').checked = false;
                document.getElementById('inlineCheckbox13').value = 0;
            }
        }
        function poolVerifiedClick() {
            if(document.getElementById('inlineCheckbox3').checked ==true && document.getElementById('inlineCheckbox4').checked ==true){
                document.getElementById('inlineCheckbox9').checked = true;
                document.getElementById('inlineCheckbox13').value = 1;
            }
            else{
                document.getElementById('inlineCheckbox9').checked = false;
                document.getElementById('inlineCheckbox13').value = 0;
            }
        }

        function marginPaidClick() {
            if(document.getElementById('inlineCheckbox5').checked ==true && document.getElementById('inlineCheckbox6').checked ==true){
                document.getElementById('inlineCheckbox10').checked = true;
                document.getElementById('inlineCheckbox12').value = 1;
            }

            else{
                document.getElementById('inlineCheckbox10').checked = false;
                document.getElementById('inlineCheckbox12').value = 0;
            }
        }
        function marginVerifiedClick() {
            if(document.getElementById('inlineCheckbox5').checked ==true && document.getElementById('inlineCheckbox6').checked ==true) {
                document.getElementById('inlineCheckbox12').value = 1;
                document.getElementById('inlineCheckbox10').checked = true;
            }
            else{
                document.getElementById('inlineCheckbox10').checked = false;
                document.getElementById('inlineCheckbox12').value = 0;
            }

        }
    </script>
@endsection
