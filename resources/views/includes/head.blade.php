<head>
    <meta charset="utf-8" />
    <title>
        @yield('title')
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Search, compare, and manage ocean freight rates across multiple carriers. With our platform, you can create a new quote in no time and give your sales a big boost!">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @if (env('APP_ENV') === 'prod' || env('APP_ENV') === 'production' || env('APP_ENV') === 'develop')
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif


    @section('css')


     <link href="/assets/vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Page Vendors -->
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(

      {'gtm.start': new Date().getTime(),event:'gtm.js'}
    );var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
          'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                              })(window,document,'script','dataLayer','GTM-MT7GKLK');
    </script>
    <!-- End Google Tag Manager -->
    <!--end::Base Styles -->
    <link rel="shortcut icon" href="/favicon.png" />
    <!--<link href="{{ asset('css/custom.css') }}" rel="stylesheet" />-->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/jqueryui-editable.css') }}" rel="stylesheet" />
    <!--<link href="{{ asset('css/header.css') }}" rel="stylesheet" />-->

    <style>
        .active-link {
            background-color: #293F66;
        }
    </style>

    @show
</head>