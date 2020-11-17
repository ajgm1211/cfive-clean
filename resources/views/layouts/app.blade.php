<!DOCTYPE html>
<html lang="en" >
@include('includes.head')
<body class="m-page--fluid"  >
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MT7GKLK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="m-grid m-grid--hor m-grid--root m-page">

    @include('includes.header')

    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop l-grid--desktop m-body">

            @section('content')
            @show

    </div>
</div>

{{--
@include('includes.footer')
--}}
<div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
    <i class="la la-arrow-up"></i>
</div>
@include('includes.scriptsbottombody')
@yield('footer')
</body>
</html>
