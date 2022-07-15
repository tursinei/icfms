<!doctype html>
<html class="fixed">
	<head>
		<!-- Basic -->
		<meta charset="UTF-8">
        @include('includes.taghead')
	</head>
	<body>
		<section class="body-sign">
            @yield('content');
        </section>
		<!-- Vendor -->
		@include('includes.footer')

	</body>
</html>
