<!DOCTYPE html>
<html>
<head>
    <title>Be right back.</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            /*color: #B0BEC5;*/
            display: table;
            font-weight: 100;
            font-family: 'Lato', sans-serif;
        }
        .container {
            text-align: center;
            display: table-cell;
            vertical-align: top;
        }
        .content {
            text-align: center;
            display: inline-block;
        }
        .title {
            font-size: 28px;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <img class="img" src="{{asset('images/logo-icon.png')}}"/>
        <div class="title"><p><b>We’ll be back in a few minutes.</b></p</div>
    </div>
</div>
</body>
</html>