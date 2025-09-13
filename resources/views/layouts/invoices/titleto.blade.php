@if ($jenis_dok == 'INVOICE')
    <p style="font-family: Arial;font-size: 10pt;margin-bottom: 25px;">No. {{ $invoice_number }}</p>
@else
    <br/>
@endif
<p style="font-family: Arial; text-align: center;  font-size: 14pt;font-weight: bold;margin-top:25px">{{ $jenis_dok }}<br>
    {!! config('app.info.title') !!}
</p>
<br/><br/>
<table style="margin-top: 20px;font-family: Arial;">
    <tr>
        <td style="vertical-align: top;padding-right: 10px;">To</td>
        <td><b>{{ $title . ' ' . $fullname }}</b></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><b>{{ $country }}</b></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><b>{{ $affiliation }}</b></td>
    </tr>
</table>
