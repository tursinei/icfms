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
                                    <th style="width: 5%" class="text-center">Payment</th>
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
    
    <script type="text/javascript">
        let table;
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
            table = refreshTableServerOn('#tbl-invoices', url, cols);
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
                    $('#div-invoice').find('.btn-bayar').click(function(e) {
                        let stoken = $(this).attr('data-token');
                        snap.pay(stoken,{
                            onSuccess : function(res){
                                console.log('Success');
                                console.log(res);
                                storePayment(res, function(respon) {
                                    let resJson = JSON.parse(respon);
                                    msgSuccess(resJson.message.head);
                                });
                            },
                            onPending : function(res){
                                console.log('Pending');
                                console.log(res);
                                if(typeof res.order_id == 'undefined'){
                                    res.order_id = $('#title-order-id').attr('data-id');
                                    console.log('adding key order');
                                    console.log(res);
                                }
                                storePayment(res, function(respon) {
                                    let resJson = JSON.parse(respon);
                                    msgSuccess(resJson.message.head);
                                });
                            },
                            onError : function(res){
                                console.log('error');
                                msgAlert('Failed payment : '.res.status_message);
                                console.error(res.status_message);
                            },
                            onClose : function(res) {
                                console.log('close');
                                console.log(res);
                            }
                        });
                    });
                }
            });
        }).on('click', '#btn-back', function(e) {
            $('#div-invoice').toggle('fast',function(){
                $('#tbl-invoice').toggle('slow');
                table.ajax.reload();
            });
        });
        // .on('click', '.btn-bayar', function(e) {
        //     let stoken = $(this).attr('data-token');
        //     snap.pay(stoken,{
        //         onSuccess : function(res){
        //             console.log('Success');
        //             console.log(res);
        //             storePayment(res, function(respon) {
        //                 let resJson = JSON.parse(respon)
        //                 msgSuccess(resJson.message.head);
        //             });
        //         },
        //         onPending : function(res){o
        //             console.log('Pending');
        //             console.log(res);
        //             storePayment(res, function(respon) {
        //                 let resJson = JSON.parse(respon)
        //                 msgSuccess(resJson.message.head);
        //             });
        //         },
        //         onError : function(res){
        //             msgAlert('Failed payment : '.res.status_message);
        //             console.error(res.status_message);
        //         }
        //    });
        // });

        let storePayment = function(res, doneFunction) {
            vAjax('',{
                url : '{{ route('payment-notification.paid') }}',
                method:'POST',
                data : res,
                done : function(r) {
                    if(typeof doneFunction == 'function'){
                        doneFunction(r);
                    }
                    $('#btn-back').trigger('click');
                }
            });
        };
    </script>
@endpush
