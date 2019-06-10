<!DOCTYPE html>
<html>
<head>
    <title>Error 404</title>

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
            font-weight: 900;
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
            font-size: 26px;
            font-weight: bold !important;
            margin-bottom: 40px;
        }
        .size-60{
            font-size: 60px;
            font-weight: bold !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <img class="img" src="{{asset('images/logo-icon.png')}}"/>
        <div class="title">
            <h1 class="size-60">404</h1>
            <p><b>The page you are looking for does not exist.</b></p>
        </div>
    </div>
</div>
</body>
</html>