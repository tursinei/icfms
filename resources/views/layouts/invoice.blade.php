<html>

<head>
    @include('layouts.invoices.taghead')
</head>

<body style="font-family: Arial;">
    @include('layouts.invoices.header')

    @include('layouts.invoices.titleto')

    @yield('konten')

    @include('layouts.invoices.payment')

    <div style="position: absolute;bottom: 0;font-size:7pt" class="footer">
        <hr style="border:1px solid black">
        Organized by Institut Teknologi Sepuluh Nopember, Universitas
        Padjadjaran, Institut Teknologi Bandung, Universitas Indonesia, and RIKEN.<br />
        Contact: Department of Physics, Institut Teknologi Sepuluh Nopember,
        Kampus ITS, Sukolilo, Surabaya 60111, Indonesia, Telp/Fax: +62-31-5943351.<br />
        <span>Email: </span>
        <a href="mailto:icaums.ims@gmail.com" style="text-decoration: none;"><span
                class="Hyperlink">icfms.id@gmail.com</span></a>
    </div>
</body>

</html>
