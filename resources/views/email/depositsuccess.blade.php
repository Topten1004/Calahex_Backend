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
            <h1 style="font-size: 3vw"><strong>Deposit successful</strong></h1>
            <h2 style="font-size: 2vw">
                {{"You have succesfully deposited ".$details['amount']." ".$details['unit']." on your Calahex account. If you do
                not recognize
                this activity please contact us at"}} <a href="mailto:customerservice@calahex.com"
                    style="color:#5b8c93">customerservice@calahex.com</a>
                <br>
                Please do not reply to this automated message.
                <img src="http://calahex.io/uploads/images/email-confirm.png"
                    style=" width: 100%; margin-top: 40px;"></img>
        </div>
    </div>
</body>

</html>
