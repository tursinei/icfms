<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog @if($isLarge??false)
        modal-lg
    @endif">

        <div class="modal-content">
            <form id="{{ $idForm }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ $modalTitle }}</h4>
                </div>
                <div class="modal-body">
                    @yield('modalBody')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                    @php
                        $hideSubmit = ($isSubmit??true)?'':'hidden';
                    @endphp
                    <button type="submit" class="btn btn-sm btn-success {{ $hideSubmit }}"><i class="fa fa-save"></i>&nbsp;Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
