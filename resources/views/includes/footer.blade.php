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
<script type="text/javascript">
    $(document).on('submit', 'form#fo-changePass',function(e) {
        e.preventDefault();
        let f = $(this);
        let btn = f.find('button[type="submit"]'), i = btn.find('i');
        let data = f.serializeArray();
        vAjax(i,{
            type : 'POST',
            data : data,
            url : '{{ route('user.changepass') }}',
            done :  function (res) {
                console.log(res);
                // f.parents("div.modal").modal('hide');
            }
        });
    }).on('click','#mn-changePass',function(ev) {
        ev.preventDefault();
        let b = $(this),i = b.find('i');
        vAjax(i,{
            url : '{{ route('user.create') }}',
            done :  function (res) {
                showModal(res);
            }
        });
    });
</script>
@stack('js');
