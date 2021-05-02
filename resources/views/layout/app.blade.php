<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.meta')

    <title>AMI Enterprise | @yield('title')</title>
    
    @include('layout.styles')
</head>

<body class="fixed-navbar">
    <div class="page-wrapper">
        @include('layout.header')

        @include('layout.sidebar')

        <div class="content-wrapper">
            @yield('content')
            
            @include('layout.footer')
        </div>
    </div>
    
    @include('layout.theme-config')

    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    
    @include('layout.scripts')
</body>

</html>