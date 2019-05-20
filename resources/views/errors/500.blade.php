<!DOCTYPE html>
<html>
<head>
    <title>Error 500</title>

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
            <h1 class="size-60">500</h1>
            <p><b>An error has occurred while we were processing your request, please try again.</b></p>
            <p><b>If it persists, contact the site administrator.</b></p>
        </div>
    </div>
</div>
</body>
</html>