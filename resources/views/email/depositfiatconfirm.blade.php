<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            line-height: 1.6em;
            margin: 0px;
        }
    </style>
</head>

<body>
    <div width="600"
        style="margin: 0 auto; color: #5b8c93; max-width: 600px !important; margin-top: 40px; background-image: linear-gradient(#5ac9ce, white, #5ac9ce); padding: 40px;"
        class="container">
        <div class="vertical-center" width="100%">
            <table>
                <tr>
                    <td>Email</td>
                    <td>{{$details['body']['email']}}</td>
                </tr>
                <tr>
                    <td>Amount</td>
                    <td>{{$details['body']['amount']}}</td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td>{{$details['body']['currency']}}</td>
                </tr>
                <tr>
                    <td>Full Name</td>
                    <td>{{$details['body']['full_name']}}</td>
                </tr>
                <tr>
                    <td>Country</td>
                    <td>{{$details['body']['country']}}</td>
                </tr>
                <tr>
                    <td>Street</td>
                    <td>{{$details['body']['street']}}</td>
                </tr>
                <tr>
                    <td>City</td>
                    <td>{{$details['body']['city']}}</td>
                </tr>
                <tr>
                    <td>Postal Code</td>
                    <td>{{$details['body']['postal_code']}}</td>
                </tr>
                <tr>
                    <td>Birthday</td>
                    <td>{{$details['body']['birthday']}}</td>
                </tr>
                <tr>
                    <td>Phone Number</td>
                    <td>{{$details['body']['phone_number']}}</td>
                </tr>
                @if($details['body']['corres_bank'])
                <tr>
                    <td>Correspondent bank:</td>
                    <td>{{$details['body']['corres_bank']}}</td>
                </tr>
                @endif
                @if($details['body']['swift_code'])
                <tr>
                    <td>Correspondent Swift Code:</td>
                    <td>{{$details['body']['swift_code']}}</td>
                </tr>
                @endif
                @if($details['body']['swift_rbcroyal'])
                <tr>
                    <td>Swift code of RBC Royal Bank (Aruba) N.V.:</td>
                    <td>{{$details['body']['swift_rbcroyal']}}</td>
                </tr>
                @endif
                @if($details['body']['address_rbcroyal'])
                <tr>
                    <td>Address of RBC Royal Bank(Aruba) N.V.:</td>
                    <td>{{$details['body']['address_rbcroyal']}}</td>
                </tr>
                @endif
                <tr>
                    <td>Beneficiary Account Name</td>
                    <td>{{$details['body']['benefit_accountname']}}</td>
                </tr>
                <tr>
                    <td>Beneficiary Account Number</td>
                    <td>{{$details['body']['benefit_accountnumber']}}</td>
                </tr>
                <tr>
                    <td>Beneficiary Address</td>
                    <td>{{$details['body']['benefi_address']}}</td>
                </tr>
                <tr>
                    <td>Details of Payment</td>
                    <td>{{$details['body']['detail_payment']}}</td>
                </tr>
            </table>
            <img src="http://calahex.io/uploads/images/email-confirm.png"
                    style=" width: 100%; margin-top: 40px;"></img>
        </div>
    </div>
</body>

</html>
