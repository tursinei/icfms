<div class="panel-body">
    <div class="invoice">
        <header class="clearfix">
            <div class="row">
                <div class="col-sm-6 mt-md">
                    <h2 class="h2 mt-none mb-sm text-dark text-weight-bold">INVOICE</h2>
                    <h4 id="title-order-id" class="h4 m-none text-dark text-weight-bold" data-id="{{ $data->order_id  }}">{{ '#' . $data->order_id }}</h4>
                </div>
                <div class="col-sm-6 text-right mt-md mb-md">
                    <address class="ib">
                        <strong>The 6th International Conference of Asian Union of Magnetics Societies 2023 (6th
                            IcAUMS 2023)</strong>
                        <br>
                        August 14 – 16, 2023
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
                        <td>Registration Fee as <strong>{{ $data->role }}</strong>
                            <br /> Name &nbsp;&nbsp;: {{ $data->userDetail->title . ' ' . $data->user->name }}
                            <br /> Paper title : {{ $data->abstract_title }}
                        </td>
                        <td class="text-right">@php
                            $mataUang = $data->currency == 'IDR' ? 'Rp' : '$';
                            $nominal = number_format($data->nominal, 0, '.', ',');
                            $nominalCurency = $mataUang . ' ' . $nominal;
                            echo $nominalCurency;
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
                                <td class="text-left">{{ $nominalCurency }}</td>
                            </tr>
                            @if ($data->currency == 'USD')
                                <tr>
                                    <td colspan="2">In Rupiah</td>
                                    <td class="text-left">{{ 'Rp ' . number_format($data->inRupiah, 0, '.', ',') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="2">Fee</td>
                                <td class="text-left">{{ 'Rp ' . $fee }}</td>
                            </tr>
                            <tr class="h4">
                                <td colspan="2">Grand Total</td>
                                <td class="text-left">{{ 'Rp ' . $total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mr-lg">
        <button class="btn btn-default ml-sm" id="btn-back"><i class="fa fa-arrow-left"></i> Back</button>
        @if ($data->status == 2 || $data->status == 3)
            <label style="font-size: large;" class="label {{ $statusLabel }} label-lg font-lg pull-right">
                {{ $status }}
            </label>
        @else
            <button type="post" data-token="{{ $data->snap_token }}"
                class="btn btn-default btn-success pull-right btn-bayar" id="bayar">Submit Invoice</button>
        @endif
    </div>
</div>
{{-- <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $data->snap_token }}"></script> --}}
{{-- <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ $data->snap_token }}"></script> --}}
<script src="{{ $data->urlSnapJs }}" data-client-key="{{ $data->snap_token }}"></script>
