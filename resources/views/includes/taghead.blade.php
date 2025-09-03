<title>@yield('title') | ICFMS {{ date('Y') }}</title>
<meta name="keywords" content="HTML5 Admin Template" />
<meta name="description" content="Porto Admin">
<meta name="author" content="Velly Coderz">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<!-- Mobile Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

<!-- Web Fonts  -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

<!-- Vendor CSS -->
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.css') }}" />

<link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/font-awesome.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/magnific-popup/magnific-popup.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/pnotify/pnotify.custom.css') }}" />

<!-- Theme CSS -->
<link rel="stylesheet" href="{{ asset('css/theme.css') }}" />

<!-- Skin CSS -->
<link rel="stylesheet" href="{{ asset('css/skins/default.css') }}" />
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}" />

<!-- Theme Custom CSS -->
{{-- <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}"> --}}
@stack('css')
<!-- Head Libs -->
<script src="{{ asset('vendor/modernizr/modernizr.js') }}"></script>
