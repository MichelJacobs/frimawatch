<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>打ち出の小槌</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

		<!-- App css -->
		<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

		<!-- icons -->
		<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

        @yield('custom_css')

    </head>

    <body class="loading" data-layout-mode="horizontal" data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

        <!-- Begin page -->
        <div id="wrapper">

            
            @include('layouts.topbar')
        
            @include('layouts.topnav-admin')
            
            @yield('content')

        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        @yield('scripts')
        
    </body>
</html>