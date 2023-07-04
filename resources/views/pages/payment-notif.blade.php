@extends('layouts.master')

@section('title', 'Payments')
@section('title2', 'Payments')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm btn-primary mb-sm"  data-id="0" id="btn-addForm"><i class="fa fa-plus"></i> Add Payment</button>
                        <a class="btn btn-success btn-sm pull-right mb-sm" href="{{ route('payment-receipt.excel') }}" target="_blank"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                        {{-- <div class="table-responsive"> --}}
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed" id="tbl-payments">
                                <thead>
                                    <tr>
                                        <th style="width: 10%" class="text-center">Date</th>
                                        <th style="width: 15%" class="text-center">Invoice</th>
                                        <th style="width: 15%" class="text-center">Email</th>
                                        <th style="width: 25%" class="text-center">Detail Personal</th>
                                        <th style="width: 10%" class="text-center">Nominal</th>
                                        <th style="width: 15%" class="text-center">Payment Date & Order ID</th>
                                        <th style="width: 10%" class="text-center">Konfirmasi</th>
                                        <th style="width: 10%" class="text-center">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/jquery.maskMoney.min.js') }}"></script>
    <script type="text/javascript">
       $(document).ready(function(evt) {
            let url = '{{ route('payment-notification.index') }}';
            let cols = [
                { data : 'tgl_invoice', name: 'tgl_invoice'},
                { data : 'invoice_number', name: 'invoice_number'},
                { data : 'email', name: 'email'},
                { data : 'detail', name: 'detail'},
                { data : 'prefnominal', name: 'prefnominal'},
                { data : 'tgl_payment_orderid', name: 'tgl_payment_orderid', className: 'text-center'},
                { data : 'konfirmasi', name: 'konfirmasi', className:'text-center'},
                { data : 'actions', name: 'actions',className:'text-center'}
            ];
            refreshTableServerOn('#tbl-payments', url, cols);
        }).on('click', '#btn-addForm',function (e) {
            let b = $(this), url = '{{ route('payment-notification.create') }}';
            vAjax(b.find('i'), {
                url : url,
                done : function (res) {
                    showModal(res).on('shown.bs.modal',function(e){
                        $(e.target).find('input[name="nominal"]').maskMoney({
                            'precision' : 0,
                            thousands : '.'
                        });
                        $(e.target).find('#invoice_id').select2({
                            dropdownParent: $('#myModal')
                        });
                    });

                }
            });
        }).on('click', '.cek-konfirm', function(e) {
            let b = $(this), url = '{{ route('konfirm-payment') }}';
            let isCek = b.is(':checked');
            vAjax('', {
                url : url,
                type : 'POST',
                data : {'is_confirm' : isCek?1:0, 'invoice_id' : b.val()},
                dataType : 'JSON',
                done : function(res){
                    msgSuccess(res.message.head);
                }
            });
        }).on('change', '#invoice_id', function(e) {
            let b = $(this), url = '{{ route('payment-notification.edit',['payment_notification' => ':id']) }}';
            url = url.replace(':id', b.val());
            vAjax('', {
                url : url,
                dataType : 'JSON',
                done : function(res){
                    $("input[name='attribut[title]']").val(res.attribut.title);
                    $("input[name='attribut[fullname]']").val(res.attribut.name);
                    $("input[name='attribut[affiliation]']").val(res.attribut.affiliation);
                    $("input[name='attribut[country]']").val(res.attribut.country);
                    $("input[name='attribut[role]']").val(res.role);
                    $("select[name='attribut[currency]']").val(res.currency);
                    $("input[name=nominal]").val(res.nominal);
                    $("input[name='attribut[abstract_title]']").val(res.abstract_title);
                }
            });
        }).on('submit', '#fo-payment', function(e){
            e.preventDefault();
            var b = $(this).find('button[type="submit"]');
            let datx = $(this).serializeArray();
            let i = b.find('i');
            vAjax(i,{
                url : '{{ route('payment-notification.store') }}',
                type : 'POST',
                data : datx,
                dataType: 'JSON',
                done : function(res){
                    $('#tbl-payments').DataTable().ajax.reload();
                    b.parents('.modal').modal('hide');
                }
            });
        }).on('click','.btn-hapus',function(params) {
            let b = $(this), i = b.find('i');
            let url = '{{ route('payment-notification.destroy',['payment_notification' => ':id']) }}';
            url = url.replace(':id',b.attr('data-id'));
            let conf = bootbox.confirm("Do you want to remove this data ?", function(ans) {
                if(ans){
                    vAjax(i, {
                        url : url,
                        type : 'DELETE',
                        dataType : 'JSON',
                        done : function(res) {
                            b.parents('tr').remove();
                        }
                    });
                }
            });
        });
    </script>
@endpush
