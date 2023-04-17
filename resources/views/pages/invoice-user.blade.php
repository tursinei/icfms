@extends('layouts.master')

@section('title', 'Invoice')
@section('title2', 'Invoice')

@section('content')
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="invoice">
                    <header class="clearfix">
                        <div class="row">
                            <div class="col-sm-6 mt-md">
                                <h2 class="h2 mt-none mb-sm text-dark text-weight-bold">INVOICE</h2>
                                <h4 class="h4 m-none text-dark text-weight-bold">{{ '#' . $data->invoice_id }}</h4>
                            </div>
                            <div class="col-sm-6 text-right mt-md mb-md">
                                <address class="ib mr-xlg">
                                    <strong>The 6th International Conference of Asian Union of Magnetics Societies 2023 (6th
                                        IcAUMS 2023)</strong>
                                    <br>
                                    August 14 – 16, 2022
                                    <br>
                                    Prama Sanur Beach Bali Hotel
                                    <br>
                                    Bali, Indonesia
                                </address>
                                <div class="ib">

                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="bill-info">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="bill-to">
                                    <p class="h5 mb-xs text-dark text-weight-semibold">To:</p>
                                    <address>
                                        {{ $data->userDetail->title . ' ' . $data->user->name }}
                                        <br>
                                        {{ $data->userDetail->affiliation }}
                                        <br>
                                        {{ $data->userDetail->country }}
                                    </address>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bill-data text-right">
                                    <p class="mb-none">
                                        <span class="text-dark">Invoice Date:</span>
                                        <span class="value">{{ date('d/m/Y', strtotime($data->tgl_invoice)) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table invoice-items">
                            <thead>
                                <tr class="h4 text-dark">
                                    <th id="cell-id" class="text-weight-semibold">Invoice Number</th>
                                    <th id="cell-desc" class="text-weight-semibold">Description</th>
                                    <th id="cell-price" class="text-center text-weight-semibold">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>Registration Fee as <strong>{{ $data->userDetail->presentation }}</strong>
                                        <br /> Name &nbsp;&nbsp;: {{ $data->userDetail->title . ' ' . $data->user->name }}
                                        <br /> Paper title : {{ implode(',', json_decode($data->abstract_title, true)) }}
                                    </td>
                                    <td class="text-right">@php
                                        $mataUang = $data->currency == 'IDR' ? 'Rp' : '$';
                                        $nominal = number_format($data->nominal, 0, '.', ',');
                                        echo $mataUang . ' ' . $nominal;
                                        $total = number_format($data->total, 0, '.', ',');
                                        $fee = number_format($data->fee, 0, '.', ',');
                                    @endphp
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="invoice-summary">
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-7">
                                <table class="table h5 text-dark">
                                    <tbody>
                                        <tr class="b-top-none">
                                            <td colspan="2">Subtotal</td>
                                            <td class="text-left">{{ $mataUang.' '. $nominal }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Fee</td>
                                            <td class="text-left">{{ $mataUang.' '. $fee }}</td>
                                        </tr>
                                        <tr class="h4">
                                            <td colspan="2">Grand Total</td>
                                            <td class="text-left">{{ $mataUang.' '. $total }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right mr-lg">
                    <button type="post" data-token="{{ $data->snap_token }}" class="btn btn-default" id="bayar">Submit Invoice</button>
                    <a href="#"  class="btn btn-primary ml-sm"><i
                            class="fa fa-print"></i> Print</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $data->snap_token }}"></script>
    <script>
        $(document).on('click','#bayar',function() {
           snap.pay('{{ $data->snap_token }}',{
                onSuccess : function(res){
                    console.log('Success');
                    console.log(res);
                },
                onPending : function(res){
                    console.log('Pending');
                    console.log(res);
                },
                onError : function(res){
                    console.error(res.message)
                }
           });
        });
    </script>
@endpush
