<table border="0" cellpadding="1" cellspacing="1" style="height:208px; width:728px">
	<tbody>
		<tr>
			<td>
                <h2 style="padding:10px 0;font-size:24px;color:#29347b">Payment completed!.</h2>
			</td>
		</tr>
		<tr>
			<td style="padding:10px;font-size:12px;font-family:arial,verdana,tahoma;color:#444;line-height:180%">
            <h4>Dear <strong>{{ $title }}&nbsp;{{ $name }}</strong><br />
			{{ $affiliation }}<br />
			{{ $country }}</h4>
			Thank you for your payment for participating in {!! config('app.info.title') !!}.<br/>
            The committee will immediately confirm your payment and will send proof of receipt of payment.
            See you soon in Bali, Indonesia<br/>
			<br />
			Best Regards,<br />
			ICFMS {{ date('Y') }} Committee<br />
		</tr>
	</tbody>
</table>
