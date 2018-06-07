<!DOCTYPE html>
<html lang="en" >
    @include('includes.head')
    <body class="m-page--fluid m--skin- m-content--skin-light2 m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
        @section('content')
        @show
        </div>
        <div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
            <i class="la la-arrow-up"></i>
        </div>
        @include('includes.scriptsbottombody')
    </body>
</html>
