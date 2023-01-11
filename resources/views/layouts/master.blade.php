<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>フリマウォッチ</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico')}}">

		<!-- App css -->
		<link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
		<link href="{{ asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css"/>

		<!-- icons -->
		<link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

        <style>
            .pro-user-name{
                display: block !important;
            }
        </style>

    </head>

    <body class="loading" data-layout-mode="horizontal" data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

        <!-- Begin page -->
        <div id="wrapper">

            
            @include('layouts.topbar')
        
            @include('layouts.topnav')
            
            @yield('content')
        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="{{ asset('assets/js/vendor.min.js')}}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.min.js')}}"></script>
        <!-- init js -->
        <script src="{{ asset('js/app.js')}}"></script>
        @yield('scripts')
        
    </body>
</html>