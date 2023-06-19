@extends('layouts.master')

@section('title', 'Invoices')
@section('title2', 'Invoices')

@section('content')
    <div class="row">
        <div class="panel" id="tbl-invoice">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm table-striped table-bordered table-fixed table-condensed"
                            id="tbl-invoices">
                            <thead>
                                <tr>
                                    <th style="width: 10%" class="text-center">Date</th>
                                    <th style="width: 20%" class="text-center">Invoice Number</th>
                                    <th style="width: 40%" class="text-center">Description</th>
                                    <th style="width: 15%" class="text-center">Nominal</th>
                                    <th style="width: 10%" class="text-center">Status</th>
                                    <th style="width: 5%" class="text-center">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel" id="div-invoice" style="display: none"></div>
    </div>
@endsection

@push('js')
    {{-- data-client-key="{{ $data->snap_token }}" --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" ></script>
    <script type="text/javascript">
        $(document).ready(function(evt) {
            let url = '{{ route('invoice-user.index') }}';
            let cols = [{
                    data: 'tgl_invoice',
                    name: 'tgl_invoice',
                    className: 'text-center'
                }, {
                    data: 'invoice_number',
                    name: 'invoice_number'
                }, {
                    data: 'description',
                    name: 'description',
                },
                {
                    data: 'terbilang',
                    name: 'terbilang'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    className: 'text-center'
                }
            ];
            refreshTableServerOn('#tbl-invoices', url, cols);
        }).on('click', '.btn-payment', function(e) {
            let b = $(this), url = '{{ route('invoice-user.form', ['invoiceId' => ':id']) }}';
            url = url.replace(':id', b.attr('data-id'));
            vAjax(b.find('i'), {
                url: url,
                dataType:'HTML',
                done: function(res) {
                    $('#div-invoice').html(res);
                    $('#tbl-invoice').toggle('fast',function(){
                        $('#div-invoice').toggle('slow');
                    });
                }
            });
        }).on('click', '#btn-back', function(e) {
            $('#div-invoice').toggle('fast',function(){
                $('#tbl-invoice').toggle('slow');
            });
        }).on('click', '.btn-bayar', function(e) {
            let stoken = $(this).attr('data-token');
            snap.pay(stoken,{
                onSuccess : function(res){
                    console.log('Success');
                    console.log(res);
                    storePayment(res, function(respon) {
                        msgSuccess(respon.message);
                    });
                },
                onPending : function(res){o
                    console.log('Pending');
                    console.log(res);
                    storePayment(res, function(respon) {
                        msgSuccess(respon.message);
                    });
                },
                onError : function(res){
                    msgAlert('Internal Server Error');
                    console.error(res.message)
                }
           });
        });

        let storePayment = function(res, doneFunction) {
            vAjax('',{
                url : '{{ route('payment-notification.paid') }}',
                method:'POST',
                data : res,
                done : function(r) {
                    if(typeof doneFunction == 'function'){
                        doneFunction(r);
                    }
                }
            });
        };
    </script>
@endpush
