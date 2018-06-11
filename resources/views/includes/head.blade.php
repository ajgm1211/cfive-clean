<head>
    <meta charset="utf-8" />
    <title>
        @yield('title')
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    @section('css')
    <link href="/assets/vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Page Vendors -->
    
    <!--end::Base Styles -->
    <link rel="shortcut icon" href="/favicon.png" />
    <!--<link href="{{ asset('css/custom.css') }}" rel="stylesheet" />-->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    @show
</head>
