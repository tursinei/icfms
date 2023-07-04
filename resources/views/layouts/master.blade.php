<!doctype html>
<html class="fixed">

<head>
    <!-- Basic -->
    <meta charset="UTF-8">
    @include('includes.taghead')
</head>

<body>
    <section class="body">
        <!-- start: header -->
        <header class="header">
            @include('includes.header')
            <!-- end: search & user box -->
        </header>
        <!-- end: header -->

        <div class="inner-wrapper">
            <!-- start: sidebar -->
            <aside id="sidebar-left" class="sidebar-left">
                @include('includes.sidebar')
            </aside>
            <!-- end: sidebar -->
            <section role="main" class="content-body">
                @include('includes.breadcumbs')
                <!-- start: page -->
                @yield('content')
                <!-- end: page -->
            </section>
        </div>
        <!-- Vendor -->
        @include('includes.footer')
        <script>
            $(document).focus(function(params) {
                vAjax('', {
                    url: '{{ route('user.isIn') }}',
                    dataType: 'JSON',
                    done: function(res) {
                        if (!res.status) {
                            window.location.reload();
                        }
                    }
                });
            });
        </script>
    </section>
</body>

</html>
