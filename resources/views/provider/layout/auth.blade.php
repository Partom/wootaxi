<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title'){{ Setting::get('site_title', 'WooCabs') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('site_icon') }}"/>


    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="{{ asset('asset/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div class="full-page-bg" style="background-image: url({{ asset('asset/img/login-bg.jpg') }});">
        <div class="log-overlay"></div>
            <div class="full-page-bg-inner">
                <div class="row no-margin">
                    <div class="col-md-12 log-left">
                        <div class="login-box-outer">
                            <div class="login-logo"><img src="{{ Setting::get('site_logo', asset('logo-black.png')) }}"></div>
                            <div class="login-box row no-margin">
                                @yield('content')
                            </div>
                            <div class="log-copy"><p class="no-margin">{{ Setting::get('site_copyright', '&copy; '.date('Y').' Hepto') }}</p></div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6 log-right"> -->
                        
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('asset/js/jquery.min.js') }}"></script>
    <script src="{{ asset('asset/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('asset/js/scripts.js') }}"></script>
</body>
</html>
