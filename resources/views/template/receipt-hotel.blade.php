@extends('layouts.invoice', ['jenis_dok' => 'RECEIPT'])

@section('konten')
    <p>Details of payment are as follow.</p>
    <P>No. {{ $invoice_number }}</P>
    <table class="noborder" cellspacing="0" cellpadding="0" style="margin-bottom: 12pt;">
        <tr>
            <td style="width: 100pt;"> Received From </td>
            <td style="width: 10pt;"><b>:</b></td>
            <td style="width: 350pt;"> <strong>{{ $title }} {{ $fullname }}</strong> </td>
        <tr>
            <td>Amount Paid</td>
            <td><b>:</b></td>
            <td>{{ $nominal }}</td>
        </tr>
        <tr>
            <td>For the payment of</td>
            <td><b>:</b></td>
            <td>
                Accommodation Prama Sanur Beach Bali Hotel, Bali, Indonesia
            </td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="0" class="tblContent">
        <tr class="tblHeader">
            <th style="width: 20%">
                Name of Person
            </th>
            <th style="width: 15%;">
                Arrival
            </th>
            <th style="width: 15%;">
                Departure
            </th>
            <th style="width: 10%;">
                #Nights
            </th>
            <th style="width: 20%;">
                Unit Price<br />
                ({{ $currency }})
            </th>
            <th style="width: 20%;">
                Total<br />
                ({{ $currency }})
            </th>
        </tr>
        <tr>
            <td>{{ $fullname }}</td>
            <td style="text-align: center;">{!! date('d-m-Y', strtotime($attribute['arrival'])) !!}</td>
            <td style="text-align: center;">{!! date('d-m-Y', strtotime($attribute['departure'])) !!}</td>
            <td style="text-align: center;">{{ $attribute['night'] }}</td>
            <td style="text-align: center;">{{ $nominal }}</td>
            <td style="text-align: center;">{{ $nominal }}</td>
        </tr>
        <tr>
            <th colspan="5">Grand Total ({{ $currency }})</th>
            <th>{{ $nominal }}</th>
        </tr>
    </table>
@endsection
