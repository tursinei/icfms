@extends('layouts.invoice', ['jenis_dok' => 'INVOICE'])

@section('konten')
    <p>Details of payment for accommodation at Prama Sanur Beach Bali Hotel, Bali, Indonesia are as follows.</p>
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
            <td style="text-align: center;">{!! date('d-m-Y', strtotime($attribut['arrival'])) !!}</td>
            <td style="text-align: center;">{!! date('d-m-Y', strtotime($attribut['departure'])) !!}</td>
            <td style="text-align: center;">{{ $attribut['night'] }}</td>
            <td style="text-align: center;">{{ $nominal }}</td>
            <td style="text-align: center;">{{ $nominal }}</td>
        </tr>
        <tr>
            <th colspan="5">Grand Total ({{ $currency }})</th>
            <th>{{ $nominal }}</th>
        </tr>
    </table>
@endsection
