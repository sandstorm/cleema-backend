<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation</title>
    <style>
        html{
            background-color: #004f59;
            color: white;
            font-family: "Atkinson Hyperlegible", serif;
        }
        .container{
            width: 100%;
            display:flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            text-align: center;
        }
        .logo{
            margin-bottom: 20px;
        }
        b{
            color:#59e5bf;
            font-size: 1.2rem;
        }
        a{
            color:#59e5bf;
        }
    </style>
</head>
<body>
    <div class="container">
        <img class="logo" src="{{URL::asset('logo.png')}}" alt="cleema Logo">
        <b>Deine Email Adresse wurde erfolgreich bestätigt.</b>
        <p>Du kannst dich jetzt in der App mit deinen Nutzerdaten anmelden. <br> Dieser Tab kann geschlossen werden.</p>
    </div>
</body>
</html>
