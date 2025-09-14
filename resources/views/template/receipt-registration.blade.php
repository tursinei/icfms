@extends('layouts.invoice', ['jenis_dok' => 'RECEIPT'])

@section('konten')
    <p>Details of payment are as follow.</p>
    <p>No. {{ $invoice_number }}</p>
    <table cellspacing="0" cellpadding="0" class="tblContent">
        <tr><td>Received From</td><td>:</td><td><strong>{{ $title }} {{ $fullname }}</strong></td></tr>
            <tr><td>Amount Paid</td><td>:</td><td>{{ $nominal }}</td></tr>
            <tr><td>For the Payment of</td><td>:</td><td>
                Registration fee as <span style="font-family: Arial; font-weight: bold;">{{ $role }}</span> in <br/>
                <strong>{!! config('app.info.title') !!}</strong><br/>
                December 10-11, 2025 – Bali, Indonesia
            </td></tr>
        </tr>
    </table>
@endsection
