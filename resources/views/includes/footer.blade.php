<script src="{{ asset('vendor/jquery/jquery.js') }}"></script>
<script src="{{ asset('vendor/jquery-browser-mobile/jquery.browser.mobile.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.js') }}"></script>
<script src="{{ asset('vendor/nanoscroller/nanoscroller.js') }}"></script>
<script src="{{ asset('vendor/magnific-popup/magnific-popup.js') }}"></script>
<script src="{{ asset('vendor/jquery-placeholder/jquery.placeholder.js') }}"></script>
<script src="{{ asset('vendor/pnotify/pnotify.custom.js') }}"></script>

<!-- Specific Page Vendor -->

<!-- Theme Base, Components and Settings -->
<script src="{{ asset('js/bootbox.all.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
{{-- <script src="{{ asset('js/theme.js') }}"></script> --}}
<!-- Theme Initialization Files -->
{{-- <script src="{{ asset('js/theme.init.js') }}"></script> --}}
<script src="{{ asset('js/ownjs.js') }}"></script>
@stack('js');
