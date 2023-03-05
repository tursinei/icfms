<table border="0" cellpadding="1" cellspacing="1" style="height:208px; width:728px">
	<tbody>
		<tr>
			<td>
			<h4>Dear <strong>{{ $title }}&nbsp;{{ $name }}</strong><br />
			{{ $affiliation }}<br />
			{{ $country }}</h4>
			</td>
		</tr>
		<tr>
			<td style="padding:10px;font-size:12px;font-family:arial,verdana,tahoma;color:#444;line-height:180%">
			<h2 style="padding:10px 0;font-size:24px;color:#29347b">Abstract Submitted.</h2>
			Thank you for submitting your abstract file through the system.<br />
			Your abstract details are the following:
			<table bgcolor="#cccccc" border="0" cellpadding="10" cellspacing="1" style="margin:20px 15px" width="650">
				<tbody>
					<tr>
						<td bgcolor="#e7e7e7" width="20%"><strong>Abstract Title</strong></td>
						<td bgcolor="#ffffff" width="80%">{{ $dataAbstract['abstract_title'] }}</td>
					</tr>
					<tr>
						<td bgcolor="#e7e7e7" width="20%"><strong>Author&rsquo;s Names</strong></td>
						<td bgcolor="#ffffff" width="80%">{{ $dataAbstract['authors'] }}</td>
					</tr>
					<tr>
						<td bgcolor="#e7e7e7" width="20%"><strong>Presentation Role</strong></td>
						<td bgcolor="#ffffff" width="80%">{{ $dataAbstract['role'] }}</td>
					</tr>
				</tbody>
			</table>
			Feel free to contact us for more information.<br />
			See you soon at IcAUMS {{ date('Y') }} conference in Bali, Indonesia.<br />
            <br/>
            If you have any queries, please do not hestitate to contact the secretariat email at <strong>icaums.ims@gmail.com</strong> <br/>
			<br />
			Best Regards,<br />
			IcAUMS {{ date('Y') }} Committee<br />
		</tr>
	</tbody>
</table>
