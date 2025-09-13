@extends('layouts.invoice', ['jenis_dok' => 'INVOICE'])

@section('konten')
    <p>Details of payment are as follow.</p>
    <table cellspacing="0" cellpadding="0" class="tblContent">
        <tr class="tblHeader">
            <th style="width: 24.2pt;">No.</th>
            <th style="width: 365.1pt;">Payment</th>
            <th style="width: 81.3pt;"> Fee </th>
        </tr>
        <tr>
            <td style="vertical-align: top; text-align: center;">1.</td>
            <td>
                <p> Registration fee as <strong>{{ $role }}</strong>
                </p>
                <table class="noborder">
                    <tr>
                        <td>Name</td>
                        <td>:</td>
                        <td><strong>{{ $title }} {{ $fullname }}</strong></td>
                    </tr>
                    @if ($role == 'AUMS Council Member')
                        <tr>
                            <td>Affiliation</td>
                            <td>:</td>
                            <td><strong>{{ $affiliation }}</strong></td>
                        </tr>
                    @else
                        <tr>
                            <td>Paper Title</td>
                            <td>:</td>
                            <td><strong>{{ $abstract_title }}</strong></td>
                        </tr>
                    @endif
                </table>
            </td>
            <td style="vertical-align: top; text-align: center;">
                <strong>{{ $nominal }}</strong>
            </td>
        </tr>
    </table>
@endsection
