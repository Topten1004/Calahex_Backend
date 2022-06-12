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
                    <td>Full Name</td>
                    <td>{{$details['body']['full_name']}}</td>
                </tr>
                <tr>
                    <td>Country</td>
                    <td>{{$details['body']['country']}}</td>
                </tr>
                <tr>
                    <td>Phone Number</td>
                    <td>{{$details['body']['phone_number']}}</td>
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
                    <td>Date</td>
                    <td>{{$details['body']['date']}}</td>
                </tr>
            </table>
            <img src="http://calahex.io/uploads/images/email-confirm.png"
                    style=" width: 100%; margin-top: 40px;"></img>
        </div>
    </div>
</body>

</html>
