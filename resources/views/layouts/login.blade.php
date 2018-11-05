<!DOCTYPE html>
<html lang="en" >
    @include('includes.head')
    <body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
        <div class="m-grid m-grid--hor m-grid--root m-page">
        @section('content')
        @show
        </div>
        <div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
            <i class="la la-arrow-up"></i>
        </div>
        @include('includes.scriptsbottombody')
    </body>
</html>
