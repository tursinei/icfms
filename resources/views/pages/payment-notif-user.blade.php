@extends('layouts.master')

@section('title', 'invoice & Receipt Payments')
@section('title2', 'Payments Notification')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item active">
                        <a href="#invoice-tab" data-toggle="tab" role="tab" class="nav-link active">Invoice</a>
                    </li>
                    <li class="nav-item">
                        <a href="#receipt-tab" data-toggle="tab" role="tab" class="nav-link">Receipt</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" role="tabpanel" id="invoice-tab">
                        <table class="table table-sm table-striped table-bordered table-fixed table-condensed" id="tbl-payments">
                            <thead>
                                <tr>
                                    <th style="width: 10%" class="text-center">Invoice Date</th>
                                    <th style="width: 5%" class="text-center">Title</th>
                                    <th style="width: 20%" class="text-center">Full Name</th>
                                    <th style="width: 10%" class="text-center">Affiliation</th>
                                    <th style="width: 10%" class="text-center">Country</th>
                                    <th style="width: 30%" class="text-center">Abstract&nbsp;Title</th>
                                    <th style="width: 15%" class="text-center">Role</th>
                                    <th style="width: 10%" class="text-center">Nominal</th>
                                    <th style="width: 10%" class="text-center">Invoice Number</th>
                                    <th style="width: 5%" class="text-center">Download PDF&nbsp;File</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" role="tabpanel" id="receipt-tab">
                        <table class="table table-sm table-striped table-bordered table-fixed table-condensed" id="tbl-receipt">
                            <thead>
                                <tr>
                                    <th style="width: 10%" class="text-center">Payment Date</th>
                                    <th style="width: 5%" class="text-center">Title</th>
                                    <th style="width: 20%" class="text-center">Full Name</th>
                                    <th style="width: 10%" class="text-center">Affiliation</th>
                                    <th style="width: 10%" class="text-center">Country</th>
                                    <th style="width: 30%" class="text-center">Abstract&nbsp;Title</th>
                                    <th style="width: 15%" class="text-center">Role</th>
                                    <th style="width: 10%" class="text-center">Nominal</th>
                                    <th style="width: 10%" class="text-center">Invoice Number</th>
                                    <th style="width: 5%" class="text-center">Download PDF&nbsp;File</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
            let url = '{{ route('invoice-notification.index') }}';
            let cols = [
                { data : 'tgl_invoice', name: 'tgl_invoice'},
                { data : 'title', name: 'title'},
                { data : 'fullname', name: 'fullname'},
                { data : 'affiliation', name: 'affiliation'},
                { data : 'country', name: 'country'},
                { data : 'abstract', name: 'abstract'},
                { data : 'role', name: 'role'},
                { data : 'prefnominal', name: 'prefnominal'},
                { data : 'invoice_number', name: 'invoice_number'},
                { data : 'actions', name: 'actions',className:'text-center'}
            ];
            refreshTableServerOn('#tbl-payments', url, cols);
            $('#tbl-payments').css('width','100%');
            // tbl.css('width','100%');
            url = '{{ route('payment-notification.index') }}';
            cols = [
                { data : 'payment_tgl', name: 'payment_tgl'},
                { data : 'title', name: 'title'},
                { data : 'fullname', name: 'fullname'},
                { data : 'affiliation', name: 'affiliation'},
                { data : 'country', name: 'country'},
                { data : 'abstract', name: 'abstract'},
                { data : 'role', name: 'role'},
                { data : 'prefnominal', name: 'prefnominal'},
                { data : 'invoice_number', name: 'invoice_number'},
                { data : 'actions', name: 'actions',className:'text-center'}
            ];
            refreshTableServerOn('#tbl-receipt', url, cols);
            $('#tbl-receipt').css('width','100%');
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
                    let role = JSON.parse(res.role);
                    let abstract = JSON.parse(res.abstract_title);
                    $("input[name='attribut[role]']").val(role.join());
                    $("select[name='attribut[currency]']").val(res.currency);
                    $("input[name=nominal]").val(res.nominal);
                    $("input[name='attribut[abstract_title]']").val(abstract.join());
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
