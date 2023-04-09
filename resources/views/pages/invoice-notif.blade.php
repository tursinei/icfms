@extends('layouts.master')

@section('title', 'Invoice')
@section('title2', 'Invoice')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm btn-primary mb-sm"  data-id="0" id="btn-addForm"><i class="fa fa-plus"></i> Add Invoice</button>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-fixed table-condensed" id="tbl-invoice">
                                <thead>
                                    <tr>
                                        <th style="width: 10%" class="text-center">Date</th>
                                        <th style="width: 15%" class="text-center">Invoice</th>
                                        <th style="width: 20%" class="text-center">Email</th>
                                        <th style="width: 5%" class="text-center">Title</th>
                                        <th style="width: 20%" class="text-center">Full Name</th>
                                        <th style="width: 10%" class="text-center">Affiliation</th>
                                        <th style="width: 10%" class="text-center">Country</th>
                                        <th style="width: 10%" class="text-center">Nominal</th>
                                        <th style="width: 10%" class="text-center">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('js/jquery.maskMoney.min.js') }}"></script>
    <script type="text/javascript">
       $(document).ready(function(evt) {
            let url = '{{ route('invoice-notification.index') }}';
            let cols = [
                { data : 'tgl_invoice', name: 'tgl_invoice'},
                { data : 'invoice_number', name: 'invoice_number'},
                { data : 'email', name: 'email'},
                { data : 'title', name: 'title'},
                { data : 'fullname', name: 'fullname'},
                { data : 'affiliation', name: 'affiliation'},
                { data : 'country', name: 'country'},
                { data : 'prefnominal', name: 'prefnominal'},
                { data : 'actions', name: 'actions',className:'text-center'}
            ];
            refreshTableServerOn('#tbl-invoice', url, cols);
        }).on('click', '#btn-addForm, .btn-edit',function (e) {
            let b = $(this), url = '{{ route('invoice-notification.create') }}';
            vAjax(b.find('i'), {
                url : url,
                done : function (res) {
                    showModal(res).on('shown.bs.modal',function(e){
                        $(e.target).find('input[name="nominal"]').maskMoney({
                            'precision' : 0,
                            thousands : '.'
                        });
                    });

                }
            });
        }).on('change', 'select[name="user_id"]', function(e){
            let b = $(this), url = '{{ route('invoice-notification.edit',['invoice_notification' => ':iduser']) }}';
            url = url.replace(':iduser', b.val());
            vAjax('', {
                url : url,
                dataType : 'JSON',
                done : function(res){
                    $("input[name='attribut[title]']").val(res.user.title);
                    $("input[name='attribut[affiliation]']").val(res.user.affiliation);
                    $("input[name='attribut[country]']").val(res.user.country);
                    $("input[name='attribut[fullname]']").val(res.user.name);
                }
            });
        }).on('submit', '#fo-user', function(e){
            e.preventDefault();
            var b = $(this).find('button[type="submit"]');
            let datx = $(this).serializeArray();
            let i = b.find('i');
            vAjax(i,{
                url : '{{ route('invoice-notification.store') }}',
                type : 'POST',
                data : datx,
                dataType: 'JSON',
                done : function(res){
                    $('#tbl-invoice').DataTable().ajax.reload();
                    b.parents('.modal').modal('hide');
                }
            });
        }).on('click','.btn-hapus',function(params) {
            let b = $(this), i = b.find('i');
            let url = '{{ route('invoice-notification.destroy',['invoice_notification' => ':id']) }}';
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
